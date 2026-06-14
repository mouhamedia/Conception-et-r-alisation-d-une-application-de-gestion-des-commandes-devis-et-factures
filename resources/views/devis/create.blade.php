@extends('layouts.app')
@section('title','Nouveau devis')
@section('page-title','Créer un devis')
@section('topbar-actions')
<a href="{{ route('devis.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--card);border:1px solid var(--border);border-radius:9px;color:var(--muted);font-size:13px;font-weight:500;text-decoration:none;">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Retour
</a>
@endsection

@section('content')
<style>
.dv-grid{display:grid;grid-template-columns:1fr 310px;gap:22px;align-items:start;}
@media(max-width:1000px){.dv-grid{grid-template-columns:1fr;}}
.f-lbl{display:block;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;}
.f-inp{width:100%;padding:10px 14px;background:var(--card2);border:1.5px solid var(--border);border-radius:10px;color:var(--text);font-size:13px;outline:none;transition:border-color .15s;font-family:inherit;}
.f-inp:focus{border-color:var(--accent);}
.f-inp::placeholder{color:var(--muted);}
.f-sec{font-size:11px;font-weight:700;color:var(--muted2,rgba(148,163,184,.35));text-transform:uppercase;letter-spacing:.1em;padding-bottom:10px;border-bottom:1px solid var(--border);margin-bottom:16px;}
.f-row2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.ligne-row{display:flex;align-items:center;gap:10px;padding:12px;background:var(--card2);border:1px solid var(--border);border-radius:10px;margin-bottom:8px;}
.ligne-del{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(239,68,68,.1);border:none;cursor:pointer;color:#f87171;flex-shrink:0;transition:background .15s;}
.ligne-del:hover{background:rgba(239,68,68,.2);}
.total-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;font-size:13px;color:var(--muted);border-bottom:1px solid var(--border2,rgba(255,255,255,.04));}
.total-row:last-child{border-bottom:none;}
.sticky-card{position:sticky;top:82px;}
</style>

<div class="dv-grid" x-data="devisForm()">
    {{-- Colonne principale --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:28px;transition:background .25s;">
        <form method="POST" action="{{ route('devis.store') }}" @submit="prepareSubmit">
            @csrf

            {{-- Client --}}
            <div class="f-sec">Informations client</div>
            <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
                <div class="f-row2">
                    <div>
                        <label class="f-lbl">Nom du client *</label>
                        <input type="text" name="client_nom" value="{{ old('client_nom') }}" required
                               class="f-inp" placeholder="Ex : Société SARL">
                        @error('client_nom')<p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="f-lbl">Email client</label>
                        <input type="email" name="client_email" value="{{ old('client_email') }}"
                               class="f-inp" placeholder="client@exemple.com">
                    </div>
                </div>
                <div class="f-row2">
                    <div>
                        <label class="f-lbl">Téléphone</label>
                        <input type="tel" name="client_telephone" value="{{ old('client_telephone') }}"
                               class="f-inp" placeholder="+213 xxx xx xx xx">
                    </div>
                    <div>
                        <label class="f-lbl">Date d'expiration *</label>
                        <input type="date" name="date_expiration" value="{{ old('date_expiration', now()->addDays(30)->format('Y-m-d')) }}" required class="f-inp">
                        @error('date_expiration')<p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Lignes produits --}}
            <div style="border-top:1px solid var(--border);padding-top:20px;margin-bottom:24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <div class="f-sec" style="margin:0;border:none;padding:0;">Produits & services</div>
                    <button type="button" @click="ajouterLigne()"
                            style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:var(--accent-bg);border:1px solid var(--accent);border-radius:8px;color:var(--accent-t);font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Ajouter une ligne
                    </button>
                </div>

                <template x-for="(ligne, index) in lignes" :key="index">
                    <div class="ligne-row">
                        <div style="flex:1;">
                            <select :name="'lignes['+index+'][produit_id]'"
                                    x-model="ligne.produit_id"
                                    @change="mettreAJourPrix(index)"
                                    class="f-inp" style="margin-bottom:0;">
                                <option value="">— Sélectionner un produit —</option>
                                @foreach($produits as $p)
                                <option value="{{ $p->id }}" data-prix="{{ $p->prix_unitaire }}">
                                    {{ $p->nom }} — {{ number_format($p->prix_unitaire,0,',',' ') }} FCFA
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="width:90px;flex-shrink:0;">
                            <input type="number" :name="'lignes['+index+'][quantite]'"
                                   x-model.number="ligne.quantite"
                                   @input="calculerTotal()"
                                   min="1" placeholder="Qté"
                                   class="f-inp" style="margin-bottom:0;text-align:center;">
                        </div>
                        <div style="width:120px;flex-shrink:0;text-align:right;">
                            <div style="font-size:13px;font-weight:700;color:var(--text);" x-text="fmt(ligne.sous_total)+' FCFA'"></div>
                            <div style="font-size:10px;color:var(--muted);">sous-total</div>
                        </div>
                        <button type="button" @click="supprimerLigne(index)" class="ligne-del">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>

                <div x-show="lignes.length===0" style="padding:20px;text-align:center;color:var(--muted);font-size:13px;background:var(--card2);border:1px dashed var(--border);border-radius:10px;">
                    Cliquez sur "Ajouter une ligne" pour inclure des produits
                </div>
            </div>

            {{-- Notes --}}
            <div style="border-top:1px solid var(--border);padding-top:20px;margin-bottom:24px;">
                <div class="f-sec">Notes</div>
                <textarea name="notes" rows="3" class="f-inp"
                          placeholder="Conditions particulières, délais de livraison, modalités de paiement…">{{ old('notes') }}</textarea>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;gap:12px;padding-top:4px;border-top:1px solid var(--border);">
                <button type="submit" class="btn-primary" style="padding:11px 24px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Créer le devis
                </button>
                <a href="{{ route('devis.index') }}" style="font-size:13px;color:var(--muted);text-decoration:none;font-weight:500;">Annuler</a>
            </div>
        </form>
    </div>

    {{-- Colonne récap --}}
    <div class="sticky-card" style="display:flex;flex-direction:column;gap:14px;">

        {{-- Récap totaux --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;transition:background .25s;">
            <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:8px;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--accent-t)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                Récapitulatif
            </div>
            <div class="total-row">
                <span>Lignes</span>
                <span style="color:var(--text2);font-weight:600;" x-text="lignes.length"></span>
            </div>
            <div class="total-row">
                <span>Sous-total HT</span>
                <span style="color:var(--text2);font-weight:600;" x-text="fmt(sousTotal)+' FCFA'"></span>
            </div>
            <div class="total-row">
                <span>TVA (18%)</span>
                <span style="color:var(--text2);font-weight:600;" x-text="fmt(montantTVA)+' FCFA'"></span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0 0;margin-top:4px;border-top:2px solid var(--border);">
                <span style="font-size:14px;font-weight:700;color:var(--text);">Total TTC</span>
                <span style="font-size:18px;font-weight:800;color:var(--accent-t);" x-text="fmt(totalTTC)+' FCFA'"></span>
            </div>
        </div>

        {{-- Produits dispo --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;transition:background .25s;">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">Catalogue ({{ count($produits) }})</div>
            @if($produits->isEmpty())
            <div style="font-size:12px;color:var(--muted);text-align:center;padding:12px;">
                <a href="{{ route('produits.create') }}" style="color:var(--accent-t);">Ajouter des produits →</a>
            </div>
            @else
            <div style="max-height:200px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;">
            @foreach($produits as $p)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:7px 10px;background:var(--card2);border-radius:8px;font-size:12px;">
                <span style="color:var(--text2);font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;margin-right:8px;">{{ $p->nom }}</span>
                <span style="color:var(--accent-t);font-weight:700;flex-shrink:0;">{{ number_format($p->prix_unitaire,0,',',' ') }}</span>
            </div>
            @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
const produitsData = @json($produits->keyBy('id'));
function devisForm(){
    return {
        lignes:[{produit_id:'',quantite:1,prix_unitaire:0,sous_total:0}],
        sousTotal:0,montantTVA:0,totalTTC:0,
        ajouterLigne(){this.lignes.push({produit_id:'',quantite:1,prix_unitaire:0,sous_total:0});},
        supprimerLigne(i){if(this.lignes.length>1){this.lignes.splice(i,1);this.calculerTotal();}},
        mettreAJourPrix(i){
            const id=this.lignes[i].produit_id;
            if(id&&produitsData[id])this.lignes[i].prix_unitaire=parseFloat(produitsData[id].prix_unitaire);
            this.calculerTotal();
        },
        calculerTotal(){
            this.lignes.forEach(l=>{l.sous_total=l.prix_unitaire*(l.quantite||0);});
            this.sousTotal=this.lignes.reduce((s,l)=>s+l.sous_total,0);
            this.montantTVA=this.sousTotal*0.18;
            this.totalTTC=this.sousTotal*1.18;
        },
        fmt(v){return new Intl.NumberFormat('fr').format(Math.round(v));},
        prepareSubmit(){return true;}
    }
}
</script>
@endpush
@endsection
