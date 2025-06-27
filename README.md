<!DOCTYPE html>
<html lang="ro" class="scholarly">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://w3c.github.io/scholarly-html/styles/scholarly.min.css">
</head>
<body>
<article>
  <h1>Specificația Cerințelor Sistemului</h1>
  <h2>Aplicația SmartFoot – Platformă de recomandări personalizate de încălțăminte</h2>

  <section id="1-introducere">
    <h2>1. Introducere</h2>
    <p>Documentul definește cerințele funcționale și nefuncționale pentru aplicația <strong>SmartFoot</strong>. Aceasta este o platformă web ce oferă recomandări de încălțăminte personalizate pentru utilizatori, în funcție de sezon, ocazie, stil vestimentar și alte filtre relevante.</p>
    <p>Documentul este structurat conform standardului IEEE 830-1998 și prezentat în format Scholarly HTML.</p>
  </section>

  <section id="2-descriere-generala">
    <h2>2. Descriere generală</h2>
    <h3>2.1 Scopul sistemului</h3>
    <p>Scopul SmartFoot este de a ajuta utilizatorii să descopere produse de încălțăminte relevante și potrivite stilului și nevoilor lor, printr-un sistem de filtrare și recomandare inteligentă.</p>

  <h3>2.2 Perspectiva sistemului</h3>
    <ul>
      <li><strong>Frontend:</strong> HTML, CSS, JavaScript</li>
      <li><strong>Backend:</strong> PHP + MySQL</li>
      <li><strong>Componente:</strong> Pagini publice (catalog, informații), conturi utilizatori, panou de administrare, API REST simplificat</li>
    </ul>

  <h3>2.3 Caracteristici utilizator</h3>
    <ul>
      <li>Clienții pot vizualiza și filtra produse, pot primi recomandări și pot consulta statistici</li>
      <li>Adminii pot adăuga, edita și șterge produse sau utilizatori</li>
      <li>Șoferii pot gestiona cursele (în cazul integrării cu transport intern)</li>
    </ul>
  </section>

  <section id="3-cerinte-functionale">
    <h2>3. Cerințe funcționale</h2>
    <h3>3.1 Autentificare & înregistrare</h3>
    <ul>
      <li>Utilizatorii se pot înregistra cu nume, email și parolă</li>
      <li>Login-ul se realizează prin email și parolă validă</li>
    </ul>
    <h3>3.2 Catalogul de produse</h3>
    <ul>
      <li>Produsele sunt afișate în funcție de sortare și filtre: stil, ocazie, brand, sezon, preț</li>
      <li>Fiecare produs are un nume, descriere, imagine, preț, brand, stil și ocazie</li>
    </ul>
    <h3>3.3 Recomandări inteligente</h3>
    <ul>
      <li>Utilizatorul poate primi sugestii în funcție de selecțiile făcute în filtre</li>
      <li>Recomandările sunt generate pe baza unui algoritm de filtrare multiplă</li>
    </ul>
    <h3>3.4 Panou administrativ</h3>
    <ul>
      <li>Adminul poate adăuga, edita și șterge produse</li>
      <li>Adminul poate vizualiza și gestiona utilizatorii</li>
      <li>Export de statistici: HTML, CSV, XML</li>
    </ul>
    <h3>3.5 Paginile informaționale</h3>
    <ul>
      <li>Pagina <code>about.html</code>: descrierea aplicației</li>
      <li>Pagina <code>help.html</code>: ghid de utilizare + întrebări frecvente</li>
    </ul>
  </section>

  <section id="4-cerinte-nefunctionale">
    <h2>4. Cerințe nefuncționale</h2>
    <ul>
      <li><strong>Performanță:</strong> timpul de răspuns sub 2 secunde la majoritatea acțiunilor</li>
      <li><strong>Portabilitate:</strong> aplicația rulează în orice browser modern (Chrome, Firefox, Safari)</li>
      <li><strong>Accesibilitate:</strong> interfață prietenoasă pentru toate categoriile de utilizatori</li>
      <li><strong>Siguranță:</strong> parolele sunt criptate, datele utilizatorilor sunt protejate, accesul la funcții admin este restricționat</li>
    </ul>
  </section>

  <section id="5-interfata-cu-utilizatorul">
    <h2>5. Interfața cu utilizatorul</h2>
    <p>Aplicația este organizată în următoarele secțiuni accesibile:</p>
    <ul>
      <li><strong>Homepage:</strong> prezentare generală și acces rapid la recomandări</li>
      <li><strong>Catalog produse:</strong> filtrare vizuală și listare a încălțămintei</li>
      <li><strong>Panou administrativ:</strong> organizat pe taburi (Dashboard, Utilizatori, Produse, Statistici)</li>
      <li><strong>Pagini statice:</strong> Despre, Ajutor</li>
    </ul>
  </section>

  <section id="6-constrangeri">
    <h2>6. Constrângeri</h2>
    <ul>
      <li>Aplicația funcționează doar cu o conexiune la internet activă (nu este PWA)</li>
      <li>Interfața de administrare necesită cont cu rol de admin</li>
      <li>Formatele de export sunt limitate la HTML/CSV/XML</li>
    </ul>
  </section>


  <section id="7-concluzie">
    <h2>7. Concluzie</h2>
    <p>SmartFoot oferă o soluție modernă și scalabilă pentru recomandarea de produse de încălțăminte. Această specificație detaliată stabilește bazele tehnice și funcționale necesare pentru dezvoltarea, testarea și întreținerea aplicației.</p>
  </section>

</article>
</body>
</html>
