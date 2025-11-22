# ProtezioNET – Segreteria di campo

**ProtezioNET – Segreteria di campo** è un software pensato per gestire in modo semplice e flessibile le informazioni necessarie al funzionamento di una segreteria di campo o di una sala operativa di Protezione Civile.

---

## Indice

1. [Utilizzo in locale](#1-utilizzo-in-locale)  
2. [Installazione su server remoto (PHP/MySQL)](#2-installazione-su-server-remoto-phpmysql)

---

# 1. Utilizzo in locale

ProtezioNET – Segreteria di campo può essere eseguito direttamente in locale.

La documentazione ufficiale e aggiornata è disponibile su:

👉 **https://help.protezionet.it/**

---

# 2. Installazione su server remoto (PHP/MySQL)

## 2.1 Ottenere il pacchetto

### Opzione A — Scaricare da GitHub

Nella sezione *Releases* del repository GitHub:

**https://github.com/linkingtechnologies/protezionet-segreteria-campo/releases**

È disponibile un file ZIP precompilato, ad esempio:

```
segreteriacampo-remote-YYYY-MM-DD.zip
```

### Opzione B — Generare il pacchetto in locale

```sh
git clone https://github.com/linkingtechnologies/peichp.git
cd peichp/
./build-segreteria-campo-remote.sh
```

---

## 2.2 Preparazione dei file in locale

Estrarre il file ZIP (`segreteriacampo-remote-YYYY-MM-DD.zip`).

Struttura prevista:

```
app/
camila/
lib/
vendor/
cli.php
index.php
```

Modificare il file:

```
app/segreteriacampo/var/1270014001.inc.php
```

Aggiornando la stringa:

```
mysql://utente:password@host:porta/nome_database
```

con i dati del proprio database MySQL.

---

## 2.3 Trasferimento dei file sul server

È possibile trasferire i file tramite un client FTP/SFTP come **FileZilla**.

### Esempio con FileZilla

1. Aprire **FileZilla**  
2. Inserire nella barra superiore:  
   - **Host:** `sftp://server.example.com` (oppure `ftp://server.example.com`)  
   - **Nome utente:** username  
   - **Password:** password  
   - **Porta:** `22` per SFTP, `21` per FTP  
3. Cliccare **Connessione rapida**  
4. Nel pannello sinistro aprire:
   ```
   segreteriacampo-remote/
   ```
5. Nel pannello destro aprire la root web, ad esempio:
   ```
   /var/www/
   ```
6. Selezionare tutti i file a sinistra e trascinarli a destra  
7. Attendere il completamento del trasferimento

---

## 2.4 Impostazione dei permessi

La cartella `var` deve avere i permessi necessari alla scrittura.

### Impostazione dei permessi con FileZilla

1. Nel pannello destro aprire:
   ```
   app/segreteriacampo/var
   ```
2. Clic destro sulla cartella **var** → **Permessi file…**  
3. Impostare:
   - Valore numerico: **777**
   - Oppure selezionare tutte le caselle (lettura, scrittura, esecuzione)  
4. Confermare con **OK**  
5. Applicare i permessi anche alle sottocartelle, se richiesto

---

## 2.5 Inizializzazione dell’applicazione

Aprire nel browser:

```
https://www.example.com/app/segreteriacampo/install.php
```

Seguire la procedura guidata.

---

## 2.6 Rimozione del file di installazione

Per motivi di sicurezza è necessario rimuovere il file `install.php`.

### Rimozione tramite FileZilla

1. Nel pannello destro aprire:
   ```
   app/segreteriacampo/
   ```
2. Individuare il file:
   ```
   install.php
   ```
3. Clic destro → **Elimina**  
4. Confermare


---

## 2.7 Accesso all’applicazione

👉 **https://www.example.com/app/segreteriacampo**

---

# Guida all’utilizzo

La guida completa all’utilizzo dell’applicazione è disponibile su:

👉 **https://help.protezionet.it**

