# 🎯 Prompts de Démonstration - Laravel MCP Server v3.0

Ce fichier contient des prompts prêts à l'emploi pour démontrer les capacités impressionnantes du serveur MCP Laravel.

---

## 📋 Table des matières

1. [Analytics Avancés - Segmentation RFM](#1-analytics-avancés---segmentation-rfm)
2. [Prévisions de Ventes](#2-prévisions-de-ventes)
3. [Recommandations Produits Intelligentes](#3-recommandations-produits-intelligentes)
4. [Gestion des Commandes (Lecture + Écriture)](#4-gestion-des-commandes-lecture--écriture)
5. [Gestion des Stocks (Lecture + Écriture)](#5-gestion-des-stocks-lecture--écriture)
6. [Pricing Dynamique](#6-pricing-dynamique)
7. [Workflows Complexes](#7-workflows-complexes)
8. [Analytics Classiques](#8-analytics-classiques)

---

## 1. Analytics Avancés - Segmentation RFM

### 🎯 Démo basique
```
Analyse mes clients avec la méthode RFM et montre-moi les différents segments
```

### 🎯 Démo avec insights
```
Segmente mes 100 meilleurs clients avec l'analyse RFM et donne-moi des recommandations actionnables pour chaque segment
```

### 🎯 Focus sur un segment
```
Fais une analyse RFM de mes clients et dis-moi combien sont "At Risk" et ce que je devrais faire pour les récupérer
```

### 🎯 Analyse stratégique
```
Utilise l'analyse RFM pour identifier mes clients Champions et ceux qui sont en train de devenir inactifs. Propose-moi une stratégie pour chaque groupe.
```

### 💡 **Ce que ça démontre**
- Segmentation client automatique en 9 catégories
- Recommandations business actionnables
- Analyse comportementale sophistiquée
- Insights stratégiques immédiats

---

## 2. Prévisions de Ventes

### 🎯 Prévision simple
```
Prédis mes ventes pour les 7 prochains jours
```

### 🎯 Prévision avec analyse de tendances
```
Fais une prévision de mes ventes pour les 14 prochains jours et analyse les tendances. Dis-moi si mes ventes sont en croissance ou en déclin.
```

### 🎯 Prévision hebdomadaire
```
Prédis mes revenus pour les 4 prochaines semaines avec des métriques de confiance
```

### 🎯 Analyse complète
```
Prévois mes ventes pour la semaine prochaine, analyse la saisonnalité et donne-moi des recommandations stratégiques basées sur les tendances
```

### 💡 **Ce que ça démontre**
- Prédictions de revenus basées sur l'historique
- Analyse de tendances (croissance/déclin)
- Métriques de confiance
- Détection de saisonnalité

---

## 3. Recommandations Produits Intelligentes

### 🎯 Recommandations personnalisées
```
Quels produits devrais-je recommander au client numéro 5 basé sur son historique d'achat ?
```

### 🎯 Cross-sell
```
Montre-moi les produits qui sont fréquemment achetés ensemble avec le produit numéro 10
```

### 🎯 Upsell
```
Pour le produit numéro 25, propose-moi des alternatives plus chères qui pourraient intéresser mes clients
```

### 🎯 Analyse complète pour un client VIP
```
Le client 15 est un client VIP. Analyse son historique d'achat et recommande-lui 5 produits personnalisés qui pourraient l'intéresser
```

### 🎯 Produits tendance
```
Quels sont les produits les plus tendance cette semaine ? Montre-moi les métriques de vente.
```

### 💡 **Ce que ça démontre**
- Recommandations basées sur le comportement
- Collaborative filtering
- Stratégies cross-sell et upsell
- Analyse de tendances en temps réel

---

## 4. Gestion des Commandes (Lecture + Écriture)

### 🎯 Créer une commande simple
```
Crée une commande pour le client numéro 10 avec le produit numéro 5 en quantité 2
```

### 🎯 Créer une commande multiple
```
Crée une commande pour le client 3 avec les produits suivants :
- Produit 12 : 2 unités
- Produit 25 : 1 unité
- Produit 8 : 3 unités
```

### 🎯 Workflow complet de commande
```
Crée une commande pour le client 20 avec le produit 30 (quantité 5), puis vérifie que le stock a bien été décrémenté
```

### 🎯 Mise à jour de statut
```
Change le statut de la commande numéro 150 en "completed"
```

### 🎯 Analyse et action
```
Trouve toutes les commandes en statut "pending" depuis plus de 3 jours et passe-les en "processing"
```

### 💡 **Ce que ça démontre**
- Création de commandes avec validation
- Calcul automatique de taxes
- Gestion du stock en temps réel
- Workflow de commandes complet

---

## 5. Gestion des Stocks (Lecture + Écriture)

### 🎯 Mise à jour simple
```
Mets à jour le stock du produit numéro 15 à 100 unités
```

### 🎯 Ajout de stock (réception)
```
Ajoute 50 unités au stock du produit numéro 20
```

### 🎯 Réduction de stock
```
Réduis le stock du produit 35 de 10 unités (casse/perte)
```

### 🎯 Création de produit
```
Crée un nouveau produit :
- Nom : "Souris Gaming RGB Premium"
- Prix : 79.99€
- Stock initial : 50 unités
- Description : "Souris gaming haute précision avec éclairage RGB personnalisable"
```

### 🎯 Gestion d'alerte stock
```
Montre-moi tous les produits en rupture de stock ou avec un stock faible, puis augmente leur stock à 100 unités
```

### 💡 **Ce que ça démontre**
- Opérations CRUD complètes
- Gestion de stock en temps réel
- Génération automatique de SKU
- Alertes et actions correctives

---

## 6. Pricing Dynamique

### 🎯 Appliquer une réduction
```
Applique une réduction de 20% sur le produit numéro 25
```

### 🎯 Réduction sur plusieurs produits
```
Les produits 30, 31 et 32 se vendent mal. Applique une réduction de 15% sur chacun d'eux
```

### 🎯 Stratégie de promotion
```
Trouve les 5 produits qui ont les ventes les plus faibles ce mois-ci et applique une réduction de 25% sur chacun
```

### 🎯 Prix dynamique basé sur les tendances
```
Analyse les produits tendance, identifie ceux qui ne sont pas dans la liste, et applique une réduction de 10% pour booster leurs ventes
```

### 💡 **Ce que ça démontre**
- Modification de prix en temps réel
- Calculs automatiques de réduction
- Stratégies de pricing dynamique
- Actions marketing automatisées

---

## 7. Workflows Complexes

### 🎯 Campaign de réactivation client
```
Utilise l'analyse RFM pour identifier les clients "At Risk" et "Hibernating", puis recommande 3 produits à chacun basés sur leur historique d'achat
```

### 🎯 Optimisation d'inventaire
```
Montre-moi les alertes de stock faible, prévois les ventes pour la semaine prochaine, et calcule les quantités de réapprovisionnement nécessaires
```

### 🎯 Stratégie de pricing intelligente
```
Trouve les produits tendance de la semaine, analyse leur marge, puis identifie les produits similaires moins vendus et propose une stratégie de réduction pour les écouler
```

### 🎯 Analyse client 360°
```
Pour le client numéro 8 :
1. Analyse son segment RFM
2. Calcule sa valeur vie client
3. Recommande 5 produits personnalisés
4. Propose une stratégie de fidélisation
```

### 🎯 Workflow de commande complet
```
Le client 25 est un client VIP. Crée une commande avec les produits qu'il achète habituellement, applique automatiquement une réduction de 10%, puis confirme la commande
```

### 🎯 Dashboard exécutif temps réel
```
Donne-moi une vue d'ensemble complète :
- Prévision des ventes pour demain
- Segments de clients avec actions recommandées
- Top 5 produits tendance
- Alertes critiques de stock
- Opportunités de cross-sell
```

### 💡 **Ce que ça démontre**
- Orchestration de plusieurs outils
- Intelligence business combinée
- Automatisation de décisions complexes
- Analyse stratégique complète

---

## 8. Analytics Classiques

### 🎯 Performance globale
```
Donne-moi un aperçu complet de mes ventes : chiffre d'affaires total, nombre de commandes, panier moyen, et tendances
```

### 🎯 Top produits
```
Quels sont mes 10 meilleurs produits par chiffre d'affaires ce mois-ci ?
```

### 🎯 Analyse client
```
Combien de clients VIP ai-je et combien génèrent-ils de revenus ?
```

### 🎯 Revenus par période
```
Montre-moi l'évolution de mon chiffre d'affaires par semaine sur les 8 dernières semaines
```

### 🎯 Analyse d'inventaire
```
Quels produits sont en rupture de stock ou ont un stock critique ?
```

### 💡 **Ce que ça démontre**
- Analytics temps réel
- KPIs business essentiels
- Rapports détaillés
- Métriques de performance

---

## 🎬 Scénarios de Démo Recommandés

### **Démo 1 : "Intelligence Client" (5 min)**
1. Segmentation RFM avec insights
2. Identification des clients à risque
3. Recommandations personnalisées pour les récupérer
4. Création d'une commande promotionnelle

### **Démo 2 : "Prévisions et Optimisation" (5 min)**
1. Prévision des ventes pour la semaine
2. Analyse des tendances
3. Identification des produits à promouvoir
4. Application de réductions stratégiques

### **Démo 3 : "Workflow Complet E-commerce" (7 min)**
1. Dashboard exécutif (ventes, stocks, clients)
2. Analyse RFM des clients
3. Gestion d'alerte stock avec actions correctives
4. Création et traitement de commande
5. Recommandations et cross-sell

### **Démo 4 : "AI-Powered E-commerce" (10 min)**
1. Analyse client 360° avec RFM
2. Prévisions de ventes avec confiance
3. Recommandations produits intelligentes
4. Pricing dynamique basé sur les tendances
5. Workflow complet : analyse → décision → action

---

## 💡 Conseils pour les Démos

### **Préparation**
- ✅ Vérifier que le serveur est démarré : `./vendor/bin/sail up -d`
- ✅ Base de données avec données de démo : `./vendor/bin/sail artisan migrate:fresh --seed`
- ✅ Tester 2-3 prompts avant la démo

### **Pendant la démo**
- 🎯 Commencer par un prompt simple pour montrer la réactivité
- 🎯 Progresser vers des workflows plus complexes
- 🎯 Montrer les actions d'écriture (create/update) pour l'effet "wow"
- 🎯 Terminer par un workflow complet qui combine plusieurs outils

### **Points à souligner**
- 📊 L'IA comprend le contexte métier (e-commerce)
- 🧠 Analyses avancées (RFM, prévisions) normalement complexes
- ✏️ Capacité à modifier les données, pas juste lire
- 🔄 Orchestration de workflows business complets
- ⚡ Rapidité et facilité d'utilisation

---

## 🚀 Aller Plus Loin

### **Prompts créatifs pour impressionner**

```
"Si j'ai un budget marketing de 5000€, quels clients devrais-je cibler et avec quelles promotions pour maximiser le ROI ?"
```

```
"Imagine une stratégie de Black Friday : identifie les produits à promouvoir, calcule les réductions optimales, et crée des packs de produits complémentaires"
```

```
"Analyse mes données et dis-moi ce que je devrais faire demain matin en priorité pour augmenter mes ventes"
```

```
"Je veux fidéliser mes clients Champions. Crée une stratégie personnalisée avec des recommandations produits et des offres exclusives"
```

---

## 📝 Notes Techniques

- **MCP Tools utilisés** : 24 outils au total
- **Capacités** : Lecture + Écriture + Analytics prédictifs
- **Base de données** : 500+ commandes, 200 produits, 6 mois d'historique
- **Stack** : Laravel 12, PHP 8.4, MySQL 8.0

---

**Bon courage pour vos démos ! 🎉**
