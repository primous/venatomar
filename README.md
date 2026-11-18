# Venatomar

Comparateur gratuit de prêts personnels (Laravel 12 · Livewire 4 · Tailwind 4 · Alpine via Livewire).
Venatomar **n'accorde pas de prêts** : il collecte une demande et la transmet par e-mail.

- 10 langues avec préfixe d'URL : `es` (défaut), `en`, `de`, `it`, `nl`, `el`, `pt`, `sk`, `sl`, `mg`. `/` redirige vers `/es`.
- Pages : accueil, demande en 5 étapes, remerciement, « Mi cuenta » (lien magique), 3 pages légales.
- Administration Filament sur `/admin`, **en français par défaut** (sélecteur FR · ES · EN en haut à droite et sur la page de connexion) :
  - **Solicitudes** : liste filtrable, fiche complète (données déchiffrées), changement de statut, renvoi des e-mails ;
  - **Opiniones** : modération des avis laissés sur le site (onglet « Pendientes »), publication, insigne « Verificado », ajout manuel ;
  - **Supresiones RGPD** : effacement définitif d'un demandeur (compte, demandes, avis) ;
  - tableau de bord chiffré.
  - Compte : renseigner `ADMIN_EMAIL` et `ADMIN_PASSWORD` (12 caractères min.) dans `.env`, puis `php artisan db:seed --class=AdminUserSeeder`.
- Avis : formulaire « Deja tu opinión » sur l'accueil et dans « Mi cuenta ». Chaque avis arrive **non publié**. Badge « Verificado » automatique seulement pour un demandeur connecté. Anti-spam : pot de miel, 4 s, 3 avis/jour/IP, liens refusés.

## Prérequis

PHP 8.2+ (extensions `intl`, `mbstring`, `pdo_mysql`, `openssl`), Composer 2, MySQL 8, Node 20+ (en local uniquement).

## Développement local

```bash
composer install
cp .env.example .env        # puis adapter (DB_CONNECTION=sqlite possible en local)
php artisan key:generate
php artisan migrate
npm install
npm run build               # ou npm run dev
php artisan serve
```

- Guide de style des composants : `/_styleguide` (uniquement si `APP_ENV=local`, `?lang=de` pour une autre langue).
- Tests : `php artisan test` (SQLite en mémoire, `Mail::fake()`).

## Déploiement sur Hostinger (mutualisé), pas à pas

1. **En local**, fabriquer le paquet (assets compilés + dépendances de production, sans `.env`, journaux ni tests) :
   ```powershell
   powershell -ExecutionPolicy Bypass -File deploy\package.ps1
   ```
   Résultat : `deploy\venatomar-deploy.zip`. Le projet local n'est pas modifié (les tests continuent de fonctionner).
2. **hPanel > Bases de données** : créer une base MySQL et un utilisateur.
3. **hPanel > E-mails** : la boîte `info@venatomar.com` existe déjà ; elle sert à la fois de `MAIL_USERNAME` et de `MAIL_FROM_ADDRESS`.
4. **Envoyer le zip** (Gestionnaire de fichiers) dans un dossier hors web, par ex. `~/venatomar`, puis l'extraire sur place.
5. **Racine web = `/public`** : dans hPanel > Sites web > Avancé, faire pointer le domaine vers `~/venatomar/public`. Si ce n'est pas possible, remplacer `public_html` par un lien symbolique :
   ```bash
   rm -rf ~/domains/[DOMAINE]/public_html && ln -s ~/venatomar/public ~/domains/[DOMAINE]/public_html
   ```
6. **SSH** :
   ```bash
   cd ~/venatomar
   cp .env.example .env && nano .env      # renseigner DB, SMTP, APP_URL
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --class=AdminUserSeeder --force   # compte admin depuis ADMIN_EMAIL / ADMIN_PASSWORD
   php artisan optimize && php artisan filament:optimize
   chmod -R 775 storage bootstrap/cache
   ```
   Retirer ensuite `ADMIN_PASSWORD` du `.env` de production (il n'est utile qu'au moment du seed).
7. **Cron** (hPanel > Avancé > Tâches cron), **chaque minute** :
   ```
   * * * * * cd ~/venatomar && php artisan schedule:run >> /dev/null 2>&1
   ```
   Le scheduler lance `queue:work --stop-when-empty` (envoi des e-mails en file), envoie chaque jour à 10 h les invitations à laisser un avis, et purge les jetons de connexion expirés.
8. **Vérifier** : soumettre une demande test, puis contrôler la réception sur info@venatomar.com et `storage/logs/mail.log`.

### E-mails

- 5 Mailables en file (`ShouldQueue`, 3 tentatives, attente 60 s / 300 s / 900 s) : `LeadReceivedInternal` (espagnol, `Reply-To` = demandeur), `LeadConfirmation` (langue du demandeur, sans IBAN, pièce d'identité ni revenus), `MagicLoginLink`, `DeletionRequested`, `ReviewInvitation`.
- **Invitation à laisser un avis** : un seul e-mail par personne, 7 jours après sa demande (dans la langue de la demande), jamais si elle a déjà laissé un avis. Réglage : `config/venatomar.php` > `reviews.invitation` (`enabled`, `delay_days`, `window_days`). Lancement manuel : `php artisan venatomar:invite-reviews`.
- **Fiches « Assurance de prêt »** (admin) : PDF d'information généré à la demande, dans 11 langues (`lang/*/insurance.php`).
- La demande est **enregistrée avant** la mise en file : un échec SMTP ne perd rien.
- Échec définitif : journal dans `storage/logs/mail.log`, marqueur `leads.mail_failed_at`. Pour relancer :
  ```bash
  php artisan venatomar:retry-mails            # --dry-run pour lister, --all pour > 7 jours
  ```

### Tester l'envoi des e-mails

```bash
php artisan venatomar:test-mail                 # envoi immédiat vers VENATOMAR_LEAD_RECIPIENT
php artisan venatomar:test-mail moi@exemple.com
php artisan queue:work                          # en local : envoie les e-mails en file d'attente
```

Hostinger : `MAIL_HOST=smtp.hostinger.com`, `MAIL_PORT=465`, `MAIL_SCHEME=smtps`, `MAIL_USERNAME` = `MAIL_FROM_ADDRESS` = la boîte (ex. info@venatomar.com), `MAIL_PASSWORD` = son mot de passe (hPanel > E-mails).

### Mise à jour

```bash
npm run build && composer install --no-dev -o     # en local, puis envoyer les fichiers
php artisan migrate --force && php artisan optimize
```

## Configuration métier

- `config/venatomar.php` : identité légale, montants, durées, TAE, exemple représentatif, seuils de dettes, avis, anti-spam, locales.
- `config/countries.php` : formats par pays (téléphone, code postal, pièce d'identité, IBAN, fichier de crédit, ordre de date et d'adresse, plaques, options).
- `config/regions/{cc}.php` : listes de régions.
- **Règle** : toute valeur entre crochets `[...]` est traitée comme absente. Les chiffres qui en dépendent sont masqués. Rien n'est inventé.

## Placeholders à remplir

### `config/venatomar.php` — identité légale

| Placeholder | Usage |
|---|---|
| `[RAISON_SOCIALE]` | pied de page, e-mails, pages légales |
| `[FORME_JURIDIQUE]` | pages légales |
| `[NIF]` | pied de page, e-mails, pages légales |
| `[REGISTRE_COMMERCE]` | pages légales |
| `[ADRESSE]` | pied de page, e-mails, pages légales |
| `[N_REGISTRE_INTERMEDIAIRE_CREDIT]` | pied de page, pages légales |
| `[LISTE_PARTENAIRES]` | CGU, confidentialité |
| `[DPO_EMAIL]` | confidentialité (optionnel) |
| `[JURIDICTION]` | CGU |
| `[DUREE_CONSERVATION]` | confidentialité |

`EMAIL_CONTACT` vaut par défaut info@venatomar.com (`VENATOMAR_CONTACT_EMAIL`).

### `config/venatomar.php` — chiffres par pays

Pour chaque code pays `{CC}` parmi `ES, GB, DE, IT, NL, GR, PT, SK, SI, MG` :

`[MONTANT_MIN_{CC}]`, `[MONTANT_MAX_{CC}]`, `[DUREE_MIN_{CC}]`, `[DUREE_MAX_{CC}]`, `[TAE_MIN_{CC}]`, `[TAE_MAX_{CC}]`, `[EX_MONTANT_{CC}]`, `[EX_DUREE_{CC}]`, `[EX_NET_{CC}]`, `[EX_TOTAL_{CC}]`, `[EX_TAE_{CC}]`, `[SEUIL_DETTE_{CC}]` (ES déjà renseigné : 8 000 €).

Effet tant qu'un placeholder reste :

- sans bornes de montant, le titre SEO, le sous-titre du hero et la question FAQ sur les montants passent à leur version courte ;
- sans exemple complet, seules les phrases fixes de l'encadré légal restent ;
- sans seuil, la question sur les dettes devient générique.

### Avis

`reviews.enabled = false`, `[NOTE_MOYENNE]`, `[NOMBRE_AVIS]`, `[SOURCE_AVIS]`. Le badge n'apparaît que si tout est rempli **et** activé. La section Opinions n'apparaît que si la table `reviews` contient des avis publiés.

- Colonnes d'un avis : `author_name`, `rating` (1–5), `title`, `body`, `reviewed_at`, `is_verified` (badge « Verificado », uniquement pour un avis lié à une vraie demande), `is_published`, `locale`.
- La note moyenne et le nombre d'avis de la section sont **calculés** à partir des avis publiés, jamais saisis à la main.
- Aperçu local : `php artisan db:seed --class=DemoReviewsSeeder` crée 6 avis factices (`source = demo`). Le seeder refuse de tourner hors `APP_ENV=local`, et ces lignes sont filtrées partout ailleurs.

### Calculateur de l'accueil

Toujours visible. Bornes des curseurs :

1. celles du pays (`amount_min`, `amount_max`, `amount_step`, `term_min`, `term_max`) dès qu'elles sont renseignées ;
2. sinon `venatomar.calculator.{EUR|GBP|MGA}` : 100–5 000 (pas 50, départ 1 400) et 3–12 mois (départ 7). Pour MGA : 100 000–5 000 000 Ar. Valeurs de la maquette, validées par le client le 16/11/2026.

Le bouton ouvre `/apply-loan?amount=…&term=…`, et le formulaire reprend ces valeurs si elles sont dans les bornes.

`reviews.highlights` (tableau de phrases, vide par défaut) affiche de courtes lignes de preuve à côté de la note moyenne, par ex. le volume mensuel de demandes. **Chiffres réels uniquement.**

### `.env`

`[DOMAINE]`, `[NOM_BASE]`, `[UTILISATEUR_BASE]`, `[MOT_DE_PASSE_BASE]`, `[ADRESSE_EXPEDITEUR]`, `[MOT_DE_PASSE_BOITE]`, et les IDs GA4 / Google Ads / Meta (optionnels).

### Autres

- `public/images/logo.png` est un **logo provisoire** (coche verte) : remplacer par le vrai logo, même nom.
- `config/regions/it.php` reprend la liste ISTAT de 107 provinces ; la réforme sarde de 2025 est à vérifier.

## Relectures nécessaires

**Juriste** : les 30 pages de `resources/views/legal/{locale}/` sont des **modèles** (CGU, confidentialité, cookies).

**Locuteur natif**, par ordre de priorité :

1. `mg` : `lang/mg.json` + `resources/views/legal/mg/*` (terminologie financière et juridique malgache).
2. `el` : `lang/el.json` + `legal/el/*`.
3. `sk` : `lang/sk.json` + `legal/sk/*`.
4. `sl` : `lang/sl.json` + `legal/sl/*` (formes plurielles du duel).
5. `nl`, `pt`, `it`, `de`, `en` : relecture de style.

L'espagnol (`es`) est la langue source.

## Points de conformité intégrés

- Aucun script tiers avant consentement (`pf_consent`, 365 jours, avec version de politique). La politique de cookies est générée à partir des cookies réellement posés et des IDs configurés.
- Champs `document_number`, `bank_account`, `mm_number`, `birthday` chiffrés (casts `encrypted`, clé `APP_KEY` : **ne jamais la perdre ni la changer**).
- Pas de BSN aux Pays-Bas. Pas de donnée personnelle dans les URL, ni dans les journaux.
- Anti-spam : pot de miel, délai de 4 s, 5 envois/h/IP, 3 demandes/jour/e-mail, 5 liens magiques/h/IP.
- Lien magique : URL signée, jeton à usage unique haché en base, 15 minutes, réponse identique que le compte existe ou non.
