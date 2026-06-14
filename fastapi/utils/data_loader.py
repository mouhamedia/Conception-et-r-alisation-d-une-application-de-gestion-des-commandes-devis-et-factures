"""
Chargement des données depuis MySQL pour les modèles IA.
Utilise SQLAlchemy pour la connexion à la base de données GestiPro.
"""
import os
from typing import Optional
import pandas as pd
from sqlalchemy import create_engine, text
from dotenv import load_dotenv

load_dotenv()

_engine = None


def get_engine():
    global _engine
    if _engine is None:
        url = os.getenv("DATABASE_URL", "mysql+pymysql://root:root@127.0.0.1:8889/gestipro")
        _engine = create_engine(url, pool_pre_ping=True)
    return _engine


def charger_devis(entreprise_id: int) -> pd.DataFrame:
    """Retourne tous les devis d'une entreprise."""
    sql = text("""
        SELECT d.id, d.numero, d.statut, d.sous_total_ht, d.total_ttc,
               d.date_emission, d.date_expiration, d.created_at
        FROM devis d
        WHERE d.entreprise_id = :eid
        ORDER BY d.created_at DESC
    """)
    try:
        with get_engine().connect() as conn:
            return pd.read_sql(sql, conn, params={"eid": entreprise_id})
    except Exception:
        return pd.DataFrame()


def charger_commandes(entreprise_id: int) -> pd.DataFrame:
    """Retourne toutes les commandes avec leurs lignes."""
    sql = text("""
        SELECT c.id, c.numero, c.statut, c.total_ttc, c.created_at,
               lc.produit_id, lc.quantite, lc.prix_unitaire_snapshot
        FROM commandes c
        LEFT JOIN ligne_commandes lc ON lc.commande_id = c.id
        WHERE c.entreprise_id = :eid
    """)
    try:
        with get_engine().connect() as conn:
            return pd.read_sql(sql, conn, params={"eid": entreprise_id})
    except Exception:
        return pd.DataFrame()


def charger_factures(entreprise_id: int) -> pd.DataFrame:
    """Retourne toutes les factures avec statut de paiement."""
    sql = text("""
        SELECT f.id, f.numero, f.statut, f.montant_paye,
               f.date_echeance, f.payee_at, f.created_at,
               c.total_ttc, c.sous_total_ht
        FROM factures f
        LEFT JOIN commandes c ON c.id = f.commande_id
        WHERE f.entreprise_id = :eid
    """)
    try:
        with get_engine().connect() as conn:
            return pd.read_sql(sql, conn, params={"eid": entreprise_id})
    except Exception:
        return pd.DataFrame()


def charger_produits(entreprise_id: int) -> pd.DataFrame:
    """Retourne le catalogue produits d'une entreprise."""
    sql = text("""
        SELECT id, nom, description, reference_sku, prix_unitaire,
               stock_actuel, stock_minimum, categorie
        FROM produits
        WHERE entreprise_id = :eid AND actif = 1
    """)
    try:
        with get_engine().connect() as conn:
            return pd.read_sql(sql, conn, params={"eid": entreprise_id})
    except Exception:
        return pd.DataFrame()
