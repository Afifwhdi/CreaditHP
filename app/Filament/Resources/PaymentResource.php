<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Customer;
use App\Models\Installment;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationGroup = 'Kredit';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Pembayaran';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('customer_id')->label('Pelanggan')
                ->options(Customer::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()->required()->live(),

            Forms\Components\Select::make('installment_id')->label('Angsuran')
                ->options(function (callable $get) {
                    $cid = $get('customer_id'); if (!$cid) return [];
                    return Installment::query()
                        ->whereHas('credit', fn($q) => $q->where('customer_id', $cid))
                        ->where('status', 'pending')->with('credit:id,customer_id')
                        ->get()
                        ->mapWithKeys(fn($i) => [
                            $i->id => "Kredit #{$i->credit_id} • Ke-{$i->installment_no} • ".
                            $i->due_date->format('d M Y') . " (Rp" . number_format($i->amount, 0, ',', '.') . ")"
                        ])->toArray();
                })->required(),

            Forms\Components\Select::make('method')
                ->options([
                    PaymentMethod::CASH->value     => 'Cash',
                    PaymentMethod::TRANSFER->value => 'Transfer',
                ])->required()->live(),

            Forms\Components\TextInput::make('amount')->numeric()->prefix('Rp')->required(),

            Forms\Components\FileUpload::make('proof_url')->label('Bukti Transfer')
                ->disk('public')
                ->directory('payments/proofs')
                ->visibility('public')
                ->acceptedFileTypes(['image/*', 'application/pdf'])
                ->maxSize(10 * 1024) 
                ->preserveFilenames()
                ->downloadable()
                ->openable()
                ->hidden(fn (callable $get) => $get('method') === PaymentMethod::CASH->value),

            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')->label('Pelanggan')->searchable(),
                Tables\Columns\TextColumn::make('installment.installment_no')->label('Ke-')->sortable(),
                Tables\Columns\TextColumn::make('method')->badge(),
                Tables\Columns\TextColumn::make('amount')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('verified_at')->since(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('verify')->label('Verifikasi')
                    ->visible(fn (): bool => Gate::allows('verify_payment'))
                    ->requiresConfirmation()
                    ->action(fn (Payment $r) => $r->update([
                        'status'      => PaymentStatus::VERIFIED,
                        'verified_at' => now(),
                        'verified_by' => auth::id(),
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            // 'view'   => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
