# testTechAlgo## testTechAlgo - Version SIMPLE

Système de planification automatique pour un laboratoire d’analyses médicales (version SIMPLE).

### 🎯 Objectif

Planifier des échantillons (`Sample`) sur des techniciens (`Technician`) et des équipements (`Equipment`) en respectant :

- Les **priorités** : STAT > URGENT > ROUTINE
- Les **compatibilités** type ↔ spécialité / équipement
- Les **créneaux horaires** des techniciens
- L’absence de **conflits** (même ressource utilisée en parallèle)

### 🧱 Structure

- `src/Sample.php` : modèle d’échantillon
- `src/Technician.php` : modèle de technicien (spécialité, horaires, réservations)
- `src/Equipment.php` : modèle d’équipement (type, disponibilité, réservations)
- `src/ReservableTrait.php` : gestion commune des réservations
- `src/Scheduler.php` : logique de planification et calcul des métriques

Datas d’exemple :

- `data-example1.php` : cas simple (1 échantillon)
- `data-example2.php` : test des priorités (STAT / URGENT / ROUTINE)
- `data-example3.php` : parallélisme + plusieurs types
- `data.php` : scénario plus complet

Tests :

- `tests/SampleTest.php`
- `tests/TechnicianTest.php`
- `tests/EquipmentTest.php`
- `tests/SchedulerTest.php`

### ⚙️ Installation

composer install### ▶️ Exécuter les scénarios de planning

```bash
php data-example1.php
php data-example2.php
php data-example3.php
php data.php
```

Chaque script affiche :

- `schedule` : planning détaillé
- `metrics` :
- `totalTime` : durée totale du planning (min)
- `efficiency` : (somme des durées d’analyses) / (temps total) \* 100
- `conflicts` : nombre de conflits détectés

### 🧪 Tests unitaires

composer test

Les tests couvrent :

- La création des modèles
- La compatibilité technicien/échantillon et équipement/échantillon
- La gestion des réservations et des disponibilités
- le tri par priorité et les métriques du `Scheduler`
