<?php

namespace App\Filament\Resources;

use App\Models\Restocked;
use App\Filament\Resources\RestockedResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;

class RestockedResource extends Resource
{
    protected static ?string $model = Restocked::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Restocked';
    protected static ?string $pluralModelLabel = 'Restocked';
    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),

                Forms\Components\Select::make('admin_id')
                    ->relationship('admin', 'name')
                    ->required(),

                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required(),

                // Menambahkan kolom price
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->label('Price')
                    ->default(0), 

                Forms\Components\DateTimePicker::make('restocked_at')
                    ->required(),

                Forms\Components\FileUpload::make('image')
                    ->label('Product Image')
                    ->image()
                    ->directory('restocked')
                    ->disk('public')
                    ->imagePreviewHeight('100')
                    ->nullable(),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes (optional)')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->height(60),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR', true) 
                    ->sortable(), 

                Tables\Columns\TextColumn::make('restocked_at')
                    ->label('Restocked Date')
                    ->dateTime(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->quantity > 0 ? 'restocked' : 'soon')
                    ->colors([
                        'success' => 'restocked',
                        'warning' => 'soon',
                    ]),

                Tables\Columns\TextColumn::make('admin.name')
                    ->label('By Admin'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'restocked' => 'Restocked',
                        'soon' => 'Soon',
                    ])
                    ->query(function (Builder $query, $state) {
                        return match ($state) {
                            'restocked' => $query->where('quantity', '>', 0),
                            'soon' => $query->where('quantity', '=', 0),
                            default => $query,
                        };
                    }),
            ])
            ->defaultSort('restocked_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestockeds::route('/'),
            'create' => Pages\CreateRestocked::route('/create'),
            'edit' => Pages\EditRestocked::route('/{record}/edit'),
        ];
    }
}
