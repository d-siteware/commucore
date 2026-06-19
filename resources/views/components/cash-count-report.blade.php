@if($counting)

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-9">
            <div>
                <flux:separator text="Scheine"/>
                <flux:table>
                    <flux:table.rows>
                        <flux:table.row>
                            <flux:table.cell>200</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->euro_two_hundred>0 ? '':'opacity-20' }}">{{ $counting->euro_two_hundred }}</span></flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>100</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->euro_one_hundred>0 ? '':'opacity-20' }}">{{ $counting->euro_one_hundred }}</span></flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>50</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->euro_fifty>0 ? '':'opacity-20' }}">{{ $counting->euro_fifty }}</span></flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>20</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->euro_twenty>0 ? '':'opacity-20' }}">{{ $counting->euro_twenty }}</span></flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>10</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->euro_ten>0 ? '':'opacity-20' }}">{{ $counting->euro_ten }}</span></flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>5</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->euro_five>0 ? '':'opacity-20' }}">{{ $counting->euro_five }}</span></flux:table.cell>
                        </flux:table.row>


                    </flux:table.rows>
                </flux:table>
            </div>
            <div>
                <flux:separator text="Münzen"/>
                <flux:table>
                    <flux:table.rows>
                        <flux:table.row>
                            <flux:table.cell>2 {{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->euro_two >0 ? '':'opacity-20' }}">{{ $counting->euro_two??0 }}</span>
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>1 {{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->euro_one >0 ? '':'opacity-20' }}">{{ $counting->euro_one??0 }}</span>
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>50 Cents</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->cent_fifty >0 ? '':'opacity-20' }}">{{ $counting->cent_fifty??0 }}</span>
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>20 Cents</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->cent_twenty >0 ? '':'opacity-20' }}">{{ $counting->cent_twenty??0 }}</span>
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>10 Cents</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->cent_ten >0 ? '':'opacity-20' }}">{{ $counting->cent_ten??0 }}</span>
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>5 Cents</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->cent_five >0 ? '':'opacity-20' }}">{{ $counting->cent_five??0 }}</span>
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>2 Cents</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->cent_two >0 ? '':'opacity-20' }}">{{ $counting->cent_two??0 }}</span>
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell>1 Cents</flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="{{ $counting->cent_one >0 ? '':'opacity-20' }}">{{ $counting->cent_one??0 }}</span>
                            </flux:table.cell>
                        </flux:table.row>
                    </flux:table.rows>
                </flux:table>
            </div>
        </section>


@endif
