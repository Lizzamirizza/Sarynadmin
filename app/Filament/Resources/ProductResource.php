<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            Textarea::make('description')->nullable(),
            TextInput::make('size')->maxLength(20)->nullable(),
            TextInput::make('stock')->numeric()->required(),
            TextInput::make('price')->numeric()->required(),

            // FileUpload::make('image')
            //     ->directory('products')
            //     ->image()
            //     ->disk('public')
            //     ->nullable(),

            FileUpload::make('image')
                    ->label('Images')
                    ->multiple()
                    ->reorderable()
                    ->directory('products')
                    ->image()
                    ->disk('public')
                    ->maxFiles(5)
                    ->nullable(),


            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->required()
                ->searchable(),

            Select::make('admin_id')
                ->label('Admin')
                ->relationship('admin', 'name')
                ->nullable()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // ImageColumn::make('image')
            //     ->label('Image')
            //     ->disk('public')
            //     ->height(50),

            ImageColumn::make('image.0')
                    ->label('Image')
                    ->disk('public')
                    ->height(50),
                

            TextColumn::make('name')->searchable(),
            TextColumn::make('category.name')->label('Category'),
            TextColumn::make('admin.name')->label('Admin'),
            TextColumn::make('stock'),
            TextColumn::make('price')->money('IDR'),
            TextColumn::make('created_at')->dateTime('d M Y H:i'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
        }
        
        
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
