<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use EbicsApi\Ebics\Contexts\FULContext;
use EbicsApi\Ebics\EbicsBankLetter;
use EbicsApi\Ebics\EbicsClient;
use EbicsApi\Ebics\Models\Bank;
use EbicsApi\Ebics\Models\BankLetter;
use EbicsApi\Ebics\Models\EmptyOrderData;
use EbicsApi\Ebics\Models\Keyring;
use EbicsApi\Ebics\Models\Order\UploadOrderResult;
use EbicsApi\Ebics\Models\User;
use EbicsApi\Ebics\Models\X509\BankX509Generator;
use EbicsApi\Ebics\Orders\FUL;
use EbicsApi\Ebics\Orders\HIA;
use EbicsApi\Ebics\Orders\HPB;
use EbicsApi\Ebics\Orders\INI;
use EbicsApi\Ebics\Services\FileKeyringManager;
use EbicsApi\Ebics\Services\KeyringManager;

final class EbicsService
{
    private const DEFAULT_EBICS_VERSION = Keyring::VERSION_25;

    private ?EbicsClient $client = null;

    private ?Keyring $keyring = null;

    private ?KeyringManager $keyringManager = null;

    public function __construct(
        private readonly SepaSettingsService $sepaSettings,
    ) {}

    public function isConfigured(): bool
    {
        return $this->sepaSettings->ebicsHost() !== ''
            && $this->sepaSettings->ebicsHostId() !== ''
            && $this->sepaSettings->ebicsPartnerId() !== ''
            && $this->sepaSettings->ebicsUserId() !== ''
            && $this->sepaSettings->ebicsPassphrase() !== '';
    }

    public function isInitialized(): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $keyringPath = $this->keyringPath();

        return file_exists($keyringPath)
            && filesize($keyringPath) > 0;
    }

    public function isReadyForUpload(): bool
    {
        if (! $this->isInitialized()) {
            return false;
        }

        $keyring = $this->getOrCreateKeyring();

        return $keyring->getBankSignatureX() !== null
            && $keyring->getBankSignatureE() !== null;
    }

    public function client(): EbicsClient
    {
        if ($this->client === null) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    public function keyringManager(): KeyringManager
    {
        if ($this->keyringManager === null) {
            $this->keyringManager = new FileKeyringManager;
        }

        return $this->keyringManager;
    }

    public function keyring(): Keyring
    {
        if ($this->keyring === null) {
            $this->keyring = $this->getOrCreateKeyring();
        }

        return $this->keyring;
    }

    public function keyringPath(): string
    {
        return storage_path('app/sepa/ebics/keyring.json');
    }

    public function initialize(): void
    {
        $client = $this->client();
        $keyring = $this->keyring();

        $client->createUserSignatures();
        $this->saveKeyring();
    }

    public function sendIni(): void
    {
        $ini = new INI;
        $this->client()->executeStandardOrder($ini);
        $this->saveKeyring();
    }

    public function sendHia(): void
    {
        $hia = new HIA;
        $this->client()->executeStandardOrder($hia);
        $this->saveKeyring();
    }

    public function downloadHpb(): void
    {
        $hpb = new HPB;
        $this->client()->executeInitializationOrder($hpb);
        $this->saveKeyring();
    }

    public function uploadXml(string $xmlContent, string $fileFormat): UploadOrderResult
    {
        $context = new FULContext;
        $context->setFileFormat($fileFormat);
        $context->setCountryCode($this->sepaSettings->ebicsCountryCode());

        $ful = new FUL($context, new EmptyOrderData($xmlContent));

        return $this->client()->executeUploadOrder($ful);
    }

    public function generateBankLetterHtml(): string
    {
        $bankLetter = $this->prepareBankLetter();

        $formatter = (new EbicsBankLetter)->createHtmlBankLetterFormatter();

        return (new EbicsBankLetter)->formatBankLetter($bankLetter, $formatter);
    }

    public function generateBankLetterPdf(): string
    {
        $bankLetter = $this->prepareBankLetter();

        $formatter = (new EbicsBankLetter)->createPdfBankLetterFormatter();

        return (new EbicsBankLetter)->formatBankLetter($bankLetter, $formatter);
    }

    public function getStatus(): array
    {
        return [
            'configured' => $this->isConfigured(),
            'initialized' => $this->isInitialized(),
            'ready_for_upload' => $this->isReadyForUpload(),
            'host' => $this->sepaSettings->ebicsHost(),
            'host_id' => $this->sepaSettings->ebicsHostId(),
            'partner_id' => $this->sepaSettings->ebicsPartnerId(),
            'user_id' => $this->sepaSettings->ebicsUserId(),
            'keyring_exists' => file_exists($this->keyringPath()),
            'keyring_path' => $this->keyringPath(),
        ];
    }

    private function prepareBankLetter(): BankLetter
    {
        return (new EbicsBankLetter)->prepareBankLetter(
            $this->createBank(),
            $this->createUser(),
            $this->keyring(),
        );
    }

    private function createClient(): EbicsClient
    {
        $keyring = $this->getOrCreateKeyring();

        $bank = $this->createBank();
        $user = $this->createUser();

        $client = new EbicsClient($bank, $user, $keyring);

        return $client;
    }

    private function createBank(): Bank
    {
        $bank = new Bank(
            $this->sepaSettings->ebicsHostId(),
            $this->sepaSettings->ebicsHost(),
        );

        $bank->setCountryCode($this->sepaSettings->ebicsCountryCode());

        $certificateGenerator = new BankX509Generator;
        $certificateGenerator->setCertificateOptionsByBank($bank);
        $this->keyring()->setCertificateGenerator($certificateGenerator);

        return $bank;
    }

    private function createUser(): User
    {
        return new User(
            $this->sepaSettings->ebicsPartnerId(),
            $this->sepaSettings->ebicsUserId(),
        );
    }

    private function getOrCreateKeyring(): Keyring
    {
        $keyringPath = $this->keyringPath();
        $passphrase = $this->sepaSettings->ebicsPassphrase();

        $keyring = $this->keyringManager()->loadKeyring(
            $keyringPath,
            $passphrase,
            self::DEFAULT_EBICS_VERSION,
        );

        $this->keyring = $keyring;

        return $keyring;
    }

    private function saveKeyring(): void
    {
        $path = $this->keyringPath();
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->keyringManager()->saveKeyring($this->keyring(), $path);
    }
}
