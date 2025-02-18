<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Form;
use App\Models\Keranjang;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TransaksiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Filament\Resources\TransaksiResource\Widgets\TransaksiWidget;


class TransaksiResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $activeNavigationIcon = 'heroicon-s-building-storefront';
    protected static ?string $navigationLabel = 'POS / KASIR';
    protected static ?string $title = 'Point Of Sales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->heading('Point Of Sales')
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_beli')
                    ->money('IDR')
                    ->hidden(),
                Tables\Columns\TextColumn::make('harga_jual')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok')
                    ->sortable(),
                Tables\Columns\TextColumn::make('diskon')
                    ->suffix('%')
                    ->sortable(),
            ])
            ->filters([
                //
                //
            ])
            
            ->actions([
                Action::make('addToCart')
                    ->label('Add')
                    ->button()
                    ->form([
                        TextInput::make('quantity')->label('Quantity')->required()->numeric()->minValue(1),
                    ])
                    ->action(function ($record, $data) {
                        $totalDiskon = $record->harga_jual * ($record->diskon / 100);
                        Keranjang::create([
                            'kode' => $record->kode,
                            'nama' => $record->nama,
                            'kategori' => $record->kategori,
                            'harga_beli' => $record->harga_beli,
                            'harga_jual' => $record->harga_jual,
                            'total_harga' => $record->harga_jual * $data['quantity'] - $totalDiskon,
                            'quantity' => $data['quantity'],
                            'diskon' => $record->diskon
                        ]);
                        Notification::make()
                            ->title('Barang Dimasukkan ke Keranjang')
                            ->icon('heroicon-s-shopping-bag')
                            ->iconColor('success')
                            ->send();
                    })
                    ->icon('heroicon-s-plus-circle'),
            ])
            ->bulkActions([]);
    }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('kode')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('nama')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('kategori')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('harga_jual')
    //                 ->numeric()
    //                 ->money('IDR')
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('stok')
    //                 ->numeric()
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('diskon')
    //                 ->numeric()
    //                 ->suffix('%')
    //                 ->sortable(),
    //         ])
    //         ->filters([
    //             //
    //             //
    //         ])
    //         ->actions([
    //             Action::make('addToCart')
    //                 ->label('Add')
    //                 ->button()
    //                 ->form([
    //                     TextInput::make('quantity')->label('Quantity')->required()->numeric()->minValue(1),
    //                 ])
    //                 ->action(function ($record, $data) {
    //                     Keranjang::create([
    //                         'kode' => $record->kode,
    //                         'nama' => $record->nama,
    //                         'kategori' => $record->kategori,
    //                         'harga_jual' => $record->harga_jual,
    //                         'total_harga' => $record->harga_jual * $data['quantity'] * (1 - $record->diskon / 100),
    //                         'kode_barang' => $record->kode_barang,
    //                         'quantity' => $data['quantity'],
    //                     ]);
    //                     Notification::make()
    //                         ->title('Barang Dimasukkan ke Keranjang')
    //                         ->icon('heroicon-s-shopping-bag')
    //                         ->iconColor('success')
    //                         ->send();
    //                 })
    //                 ->icon('heroicon-s-plus-circle'),
    //         ])
    //         ->bulkActions([
    //         ]);
    // }

    
    
    public static function getWidgets(): array

    {
        return [
            TransaksiWidget::class,
        ];
    }

    public function render()
    {
        return view('livewire.TransaksiResource.php');
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
            
        ];
    }
}
