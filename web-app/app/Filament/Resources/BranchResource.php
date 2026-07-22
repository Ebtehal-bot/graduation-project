<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;
    protected static ?string $navigationIcon = 'heroicon-o-office-building';

    public static function getModelLabel(): string
    {
        return __('sidebar.model.branch');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.model.branches_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.branches');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الفرع')
                            ->required(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                // تحويل حقل المحافظة إلى قائمة منسدلة بجميع المحافظات اليمنية
                                Forms\Components\Select::make('governorate')
                                    ->label('المحافظة')
                                    ->options([
                                        'صنعاء' => 'صنعاء',
                                        'عدن' => 'عدن',
                                        'تعز' => 'تعز',
                                        'الحديدة' => 'الحديدة',
                                        'حضرموت' => 'حضرموت',
                                        'إب' => 'إب',
                                        'ذمار' => 'ذمار',
                                        'حجة' => 'حجة',
                                        'عمران' => 'عمران',
                                        'مأرب' => 'مأرب',
                                        'صعدة' => 'صعدة',
                                        'الجوف' => 'الجوف',
                                        'البيضاء' => 'البيضاء',
                                        'أبين' => 'أبين',
                                        'لحج' => 'لحج',
                                        'شبوة' => 'شبوة',
                                        'المهرة' => 'المهرة',
                                        'سقطرى' => 'سقطرى',
                                        'ريمة' => 'ريمة',
                                        'الضالع' => 'الضالع',
                                        'المحويت' => 'المحويت',
                                    ])
                                    ->searchable()
                                    ->required(),

                                Forms\Components\TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->required(),

                                Forms\Components\TextInput::make('address')
                                    ->label('العنوان التفصيلي')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الفرع')
                    ->sortable(),

                Tables\Columns\TextColumn::make('governorate')
                    ->label('المحافظة'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // إضافة زر تصدير PDF مخصص لكل فرع / محافظة
                Tables\Actions\Action::make('export_pdf')
                    ->label('تصدير PDF')
                    ->color('success')
                    ->icon('heroicon-o-document-download')
                    ->url(fn (Branch $record) => route('branches.export-pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // ربط الأيتام بالفرع ليعرضهم تلقائياً في الأسفل عند استعراض الفرع
            BranchResource\RelationManagers\OrphansRelationManager::class,
        ];
    }

   public static function getWidgets(): array
    {
        return [
            // استخدام المسار الصحيح بتبديل المائل المائل بكلاس الـ Widget مباشرة
            \App\Filament\Resources\BranchResource\Widgets\BranchOrphansChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}