<style>
.fg{display:grid;grid-template-columns:1fr 300px;gap:22px;align-items:start;}
@media(max-width:900px){.fg{grid-template-columns:1fr;}}
.f-lbl{display:block;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:6px;}
.f-inp{width:100%;padding:10px 14px;background:var(--card2);border:1.5px solid var(--border);border-radius:10px;color:var(--text);font-size:13px;outline:none;transition:border-color 0.15s;font-family:inherit;resize:vertical;}
.f-inp:focus{border-color:var(--accent);}
.f-inp::placeholder{color:var(--muted);}
.f-sec{font-size:11px;font-weight:700;color:var(--muted2,rgba(148,163,184,0.35));text-transform:uppercase;letter-spacing:0.1em;padding-bottom:10px;border-bottom:1px solid var(--border);margin-bottom:16px;}
.f-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.f-field{display:flex;flex-direction:column;gap:0;}
.f-group{display:flex;flex-direction:column;gap:14px;}
.tip-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;transition:background 0.25s;}
.tip-item{display:flex;align-items:flex-start;gap:8px;font-size:12px;color:var(--muted);margin-bottom:10px;line-height:1.5;}
.tip-item:last-child{margin-bottom:0;}
.tip-dot{width:6px;height:6px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:5px;}
/* Combobox catégorie */
.cat-dd{position:absolute;top:calc(100% + 4px);left:0;right:0;background:var(--card);border:1.5px solid var(--accent);border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.15);z-index:200;overflow:hidden;max-height:200px;overflow-y:auto;}
.cat-opt{padding:9px 13px;cursor:pointer;font-size:13px;color:var(--text);display:flex;align-items:center;gap:7px;}
.cat-opt:hover{background:var(--card2);}
.cat-new{padding:9px 13px;cursor:pointer;font-size:12px;font-weight:700;color:var(--accent-t);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:7px;}
.cat-new:hover{background:var(--accent-bg);}
</style>

<div class="fg">
    {{-- Colonne principale --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:28px;transition:background 0.25s;">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if($method === 'PUT') @method('PUT') @endif

            <div class="f-group">
                <div class="f-sec">Informations générales</div>

                <div class="f-field">
                    <label class="f-lbl">Nom du produit *</label>
                    <input type="text" name="nom" value="{{ old('nom', $produit?->nom) }}" required autofocus
                           class="f-inp" placeholder="Ex : Ordinateur portable HP 15">
                    @error('nom')<p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>@enderror
                </div>

                <div class="f-row">
                    <div class="f-field">
                        <label class="f-lbl">Référence / SKU <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--muted2);font-size:10px;">(facultatif)</span></label>
                        <input type="text" name="reference_sku" value="{{ old('reference_sku', $produit?->reference_sku) }}"
                               class="f-inp" placeholder="Auto-générée si vide" style="font-family:monospace;">
                        @error('reference_sku')<p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    {{-- Combobox catégorie --}}
                    <div class="f-field" x-data="{
                        val: '{{ old('categorie', $produit?->categorie ?? '') }}',
                        open: false,
                        cats: {{ Js::from($categories ?? collect()) }},
                        get filtered() {
                            const q = this.val.toLowerCase().trim();
                            return q ? this.cats.filter(c => c.toLowerCase().includes(q)) : this.cats;
                        },
                        get isNew() { return this.val.trim().length > 0 && !this.cats.map(c=>c.toLowerCase()).includes(this.val.toLowerCase().trim()); },
                        pick(c) { this.val = c; this.open = false; }
                    }" @click.outside="open=false">
                        <label class="f-lbl">Catégorie</label>
                        <div style="position:relative;">
                            <input type="text" name="categorie" x-model="val"
                                   @focus="open=true" @input="open=true"
                                   class="f-inp" placeholder="Saisir ou choisir…"
                                   autocomplete="off" style="padding-right:36px;">
                            <button type="button" @click="open=!open" tabindex="-1"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--muted);">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="open && (filtered.length > 0 || isNew)" class="cat-dd" style="display:none;">
                                {{-- Créer nouvelle catégorie --}}
                                <template x-if="isNew">
                                    <div class="cat-new" @click="pick(val)">
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Créer "<span x-text="val"></span>"
                                    </div>
                                </template>
                                {{-- Catégories existantes --}}
                                <template x-for="cat in filtered" :key="cat">
                                    <div class="cat-opt" @click="pick(cat)">
                                        <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        <span x-text="cat"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <p style="font-size:11px;color:var(--muted);margin-top:4px;">Choisissez une catégorie existante ou tapez pour en créer une nouvelle</p>
                    </div>
                </div>

                <div class="f-field">
                    <label class="f-lbl">Prix unitaire HT (DZD) *</label>
                    <div style="position:relative;">
                        <input type="number" name="prix_unitaire" value="{{ old('prix_unitaire', $produit?->prix_unitaire) }}"
                               required min="0" step="1" class="f-inp" placeholder="0" style="padding-right:60px;">
                        <span style="position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--muted);pointer-events:none;">DZD</span>
                    </div>
                    @error('prix_unitaire')<p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>@enderror
                </div>

                <div style="border-top:1px solid var(--border);padding-top:16px;">
                    <div class="f-sec" style="margin-top:2px;">Gestion du stock</div>
                    <div class="f-row">
                        <div class="f-field">
                            <label class="f-lbl">Stock actuel *</label>
                            <input type="number" name="stock_actuel" value="{{ old('stock_actuel', $produit?->stock_actuel ?? 0) }}"
                                   required min="0" class="f-inp" placeholder="0">
                            @error('stock_actuel')<p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>@enderror
                        </div>
                        <div class="f-field">
                            <label class="f-lbl">Stock minimum *</label>
                            <input type="number" name="stock_minimum" value="{{ old('stock_minimum', $produit?->stock_minimum ?? 1) }}"
                                   required min="0" class="f-inp" placeholder="5">
                            <p style="font-size:11px;color:var(--muted);margin-top:4px;">Seuil d'alerte automatique</p>
                        </div>
                    </div>
                </div>

                <div style="border-top:1px solid var(--border);padding-top:16px;">
                    <div class="f-sec" style="margin-top:2px;">Description</div>
                    <div class="f-field">
                        <textarea name="description" rows="4" class="f-inp"
                                  placeholder="Description optionnelle du produit, caractéristiques, détails…">{{ old('description', $produit?->description) }}</textarea>
                    </div>
                </div>

                {{-- Statut --}}
                <div style="display:flex;align-items:center;gap:10px;padding:14px;background:var(--card2);border:1px solid var(--border);border-radius:10px;">
                    <input type="checkbox" name="actif" id="actif" value="1"
                           style="width:17px;height:17px;accent-color:var(--accent);cursor:pointer;"
                           {{ old('actif', $produit ? $produit->actif : true) ? 'checked' : '' }}>
                    <label for="actif" style="cursor:pointer;font-size:13px;font-weight:500;color:var(--text);">
                        Produit actif
                        <span style="display:block;font-size:11px;color:var(--muted);font-weight:400;margin-top:1px;">Disponible dans les devis et le catalogue</span>
                    </label>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border);">
                <button type="submit" class="btn-primary" style="padding:11px 24px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ $submitLabel }}
                </button>
                <a href="{{ route('produits.index') }}" style="font-size:13px;color:var(--muted);text-decoration:none;font-weight:500;">Annuler</a>
            </div>
        </form>
    </div>

    {{-- Colonne info --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="tip-card">
            <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:8px;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--accent-t)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                Guide
            </div>
            <div class="tip-item"><div class="tip-dot"></div><span>La <strong style="color:var(--text);">Référence SKU</strong> permet d'identifier uniquement le produit dans les exports.</span></div>
            <div class="tip-item"><div class="tip-dot"></div><span>Le <strong style="color:var(--text);">stock minimum</strong> déclenche une alerte automatique quand le stock atteint ce seuil.</span></div>
            <div class="tip-item"><div class="tip-dot"></div><span>Seuls les produits <strong style="color:var(--text);">actifs</strong> apparaissent dans le formulaire de création de devis.</span></div>
            <div class="tip-item"><div class="tip-dot"></div><span>Le prix HT est hors taxes. La TVA (18%) est calculée automatiquement dans les devis.</span></div>
        </div>

        @if($produit)
        <div class="tip-card">
            <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:12px;">Informations</div>
            <div style="display:flex;flex-direction:column;gap:8px;font-size:12px;color:var(--muted);">
                <div style="display:flex;justify-content:space-between;">
                    <span>Créé le</span>
                    <span style="color:var(--text2);font-weight:500;">{{ $produit->created_at->format('d/m/Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span>Dernière modification</span>
                    <span style="color:var(--text2);font-weight:500;">{{ $produit->updated_at->diffForHumans() }}</span>
                </div>
                @php try { $nbDevis = $produit->lignesDevis()->count(); } catch(\Exception $e) { $nbDevis = '—'; } @endphp
                <div style="display:flex;justify-content:space-between;">
                    <span>Utilisé dans</span>
                    <span style="color:var(--accent-t);font-weight:600;">{{ $nbDevis }} devis</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
