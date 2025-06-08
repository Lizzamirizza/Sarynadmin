<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('order_id')
                ->relationship('order', 'id')
                ->required(),

            TextInput::make('status')->required(),
            DateTimePicker::make('payment_date')->nullable(),
            TextInput::make('amount')->numeric()->required(),
            TextInput::make('midtrans_transaction_id')->nullable(),
            TextInput::make('midtrans_status')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.id')->label('Order #'),
                BadgeColumn::make('status')->label('Status')->colors([
                    'gray' => 'pending',
                    'success' => 'settlement',
                    'danger' => 'cancel',
                    'warning' => 'expire',
                ]),
                TextColumn::make('payment_date')->dateTime(),
                TextColumn::make('amount')->money('IDR'),
                TextColumn::make('midtrans_transaction_id')->label('Transaction ID'),
                TextColumn::make('midtrans_status')->label('Midtrans Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
