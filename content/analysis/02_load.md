---
Title: Analysis | Prestanda test
Description: This is tech page
Template: IndivAnalysis
---


Prestanda test
=======================
![Leaf](%assets_url%/img/leaf_256x256.png)
![Leaf](../image/leaf_256x256.png?width=75%)
![Leaf](../image/leaf_256x256.png?width=50%)

Urval
-----------------------

Jag valde att analysera twitch, youtube och reddit

Metod
-----------------------

Jag använder mig utav pagespeed.web.dev för att mäta laddningshastigheten på hemsidorna och diagnosering för att kunna se förbättrings möjligheter. För denna analysen så använda jag hemsidan för stationära datorer.

Resultat
-----------------------

<iframe src="https://docs.google.com/spreadsheets/d/e/2PACX-1vSDsvXtAVW0S2kDA7Cp9Ynid4O_Qv1FwJ2Je9e3TtGhhJW4H5_pwv4M36VbwPEXLTqKJThYprPclEpp/pubhtml?widget=true&amp;headers=false" width=100% height=230px></iframe>

Twitch
-----------------------
Twitch behöver reducera javascript som inte används enligt pagespeed.web.dev samt använder dem bilder som inte har rätt storlek och skicka bilder i modernare format.

Resursen nämner även att sidan hade gått snabbare med mer effektiv cachelagring. Twitch har även ett stort dom träd på ca 4500~ element.
Passiv lyssnare änvänds inte för att förbättra rullningsprestanda, arbetsbelstningen  på modertråden mycket hög på 7s, runtime på javascript ca 5s.

Reddit
-----------------------
Reddit gav mycket möjliga förbättringar där det gällde svarstid om man hade tagit bort JavaScript som inte används ca 1s samt även 1s snabbare om man hade skickat bilder i modernare bildformat men det är user content så det förväntades.

Reddit hade även ett mycket stort DOM träd på ca 5000 element.

Devtools sa även att dem använder 'enorm näterverksbelastning' på 5500~Kibit en stor del av belastning var deras nya chat funktion.

Youtube
-----------------------
Även youtube tog upp mycket "extra" tid på inläsning av javascript som inte användes.

På diagnostik även youtube hade ett väldigt stort DOM träd på 8500~ element det är väl något man kan förvänta sig på stora sidor?
Det nämns även att de bör ha passiva lyssnare för att förbättra rullningsprestanda, och stor körningstid för javascript och stor belastning på modertråden.



Bilder
-----------------------
Twitch
![Twitch](../image/twitch.png?width=75%)
Reddit
![Reddit](../image/reddit.png?width=75%)
Youtube
![Youtube](../image/youtube.png?width=75%)

Analys
-----------------------
Så det verkade som att sidorna oftast hade samma "problem" om man skulle kalla det så men personligen så var det rätt förväntat för vissa av problemen som t.ex. ett stort DOM träd och bilder i fel format osv, det är så det blir om man prioriterar user experience genom att ge tillgång till flera olika bildformat, vet inte om det finns något smart sätt att göra om user submitted content till ens format av val men om man hade kunnat göra det på ett enkelt sätt så skulle det varit bra för sidornas prestanda. Vad det gäller dom träden så är även det förväntat för mig i alla fall då stora sidor enligt mig lär ha många element på deras sidor. Jag använde en del mindre sidor som jag kännde till för referens och dem hade alla mycket bättre resultat enligt pagespeed.web.dev och oftast inga problem med för mycket element eller problem med javascript. Jag är osäker på hur dem mäter hur mycket är för mycket element på ett dom träd men när jag kollade igenom https://pcpartpicker.com/ som ett test så hade även den för många dom element så det kanske skalas på sidans storlket eller något är osäker och tänker inte dra någon slutsats.

Det verkade som att javascript låg bakom många av prestanda fallen då det var mycket oanvänd javascript eller javascript som utfördes för sakta så det är något man själv kan tänka på framöver just att skriva bra och funktionell kod som inte drar för mycket prestanda iväg från själva hemsidan.

Det verkade vara rätt betydligt att använda rätt bildformat och rätt scaling för hemsidans prestanda och det är även något man själv bör tänka på särskillt om man gör en egen sida utan user content så finns det inte någon speciellt bra anledning till att inte använda moderna eller format som fungerar bra för prestanda samt har bra/rätt bildkvalite även om själva prestandan inte saktas ner lika mycket som den skulle göra på en hemsida där felet kanske upprepas ett flertal gånger mer en den hade gjort på våra sidor.

Det var välgit intressant att kolla in på dessa större sidorna och se hur bra sidorna ändå fungerar trots hur stora dem är både på antalet videor bilder och mängden trafik som sidorna får. Trotts det så fick dem väldigt dåliga resultat av googles verktyg.

Jag märkte att det var mycket mindre element som lästes in på mobil sidorna antagligen för att öka inläsningen på enheter med mindre proccesing kraft i sig. När jag gjorde testerna så märkte jag även att sidorna hade stor variation och om man rensar med ctrl shift r så är det betydligt mer data som behövs vilket tyder på att dem använder sig mycket av cache för att få snabbar inläsningstid då mindre data skickas över nätet det var så jag förklarade det i alla fall. Jag tror även att laddningstiderna på deras egna appar antagligen är mycket snabbare än vad dem är på en mobilversion av sidorna men det är bara tänk.

Rangordning
----------------------
Jag väljer att göra min ranking baserat på sidornas sammanlagda prestanda på både pc och mobile baserat på resultatet från googles mätnings sida.
Twitch = 76
Reddit = 69
Youtube = 67
Förvånadsvärt så kommer twitch i första plats mycket pga deras starka mobil site.

Gräns för laddningstid
----------------------
För att gemföra dessa hemsidorna så använder jag mig av avg på tre sidor som klarar av googles test på bra sätt pcpartpicker dbwebb.se och w3schools.
Deras avg load time ligger på runt 600ms.

Mitt urval av webbsidor klarar självklart inte av denna gräns eller en gräns som vore högre, detta reflekteras också även i googles mätnings sida då inga av hemsidorna fick godkänt men det var något jag förväntade mig då dessa är super stora sidor med väldigt mycket content och webbtrafik dessa sidor tog oftast flera sekunder att lägga in medans dem jag mätte dem emot samt majoriteten av webbsidor laddar in mycket snabbare för att dem sidorna är mycket mindre.

Referenser
-----------------------
https://pagespeed.web.dev/
https://docs.google.com/spreadsheets/d/1LBAIYIeX1EXIziiz8I0jSMjGJYMLC7bBdI7Tii1LARM/edit?usp=sharing
https://www.youtube.com/
https://www.reddit.com/
https://www.twitch.tv/
https://pcpartpicker.com/
https://www.w3schools.com/
https://dbwebb.se/

Övrigt
-----------------------

Av David Palmgren