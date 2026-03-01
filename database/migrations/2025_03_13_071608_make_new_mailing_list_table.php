<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $lists = DB::table('mailinglists')->get();

        // TODO: Drop table in future time
        //
        //        if (Schema::hasTable('mailinglists')) {
        //            Schema::dropIfExists('mailinglists');
        //        }
        //

        Schema::create('mailing_lists', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('email')->unique();
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('update_on_events')->default(false);
            $table->boolean('update_on_articles')->nullable()->default(false);
            $table->boolean('update_on_notifications')->nullable()->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_token')->nullable();
            $table->string('locale')->nullable();
        });

        if ($lists->count() > 0) {
            foreach ($lists as $list) {
                DB::table('mailing_lists')
                    ->insert([
                        'created_at' => $list->created_at,
                        'updated_at' => $list->updated_at,
                        'email' => $list->email,
                        'terms_accepted' => true,
                    ]);
            }
        }

    }
};
