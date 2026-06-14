@extends('layouts.app')
@section('title','Nouveau produit')
@section('page-title','Ajouter un produit')
@section('topbar-actions')
<a href="{{ route('produits.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--card);border:1px solid var(--border);border-radius:9px;color:var(--muted);font-size:13px;font-weight:500;text-decoration:none;">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Retour
</a>
@endsection
@section('content')
@include('produits._form', ['produit' => null, 'action' => route('produits.store'), 'method' => 'POST', 'submitLabel' => 'Créer le produit'])
@endsection
