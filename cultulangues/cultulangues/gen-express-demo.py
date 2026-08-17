import re

with open('detail-concepts.html','r',encoding='utf-8') as f:
    h = f.read()

# ── ORDER MATTERS: most specific/longest strings FIRST ──

# 1) CTA buttons (BEFORE service name replacement to avoid "votre Français Express")
h = h.replace("R\u00e9servez votre formation en solo \u2192","R\u00e9servez Fran\u00e7ais Express \u2192")
h = h.replace("R\u00e9servez votre formation \u2192","R\u00e9servez Fran\u00e7ais Express \u2192")
h = h.replace("Pr\u00eat \u00e0 commencer votre formation en solo ?","Pr\u00eat \u00e0 ma\u00eetriser le fran\u00e7ais en 4 semaines ?")
h = h.replace("Pr\u00eat \u00e0 booster votre fran\u00e7ais ?","Pr\u00eat \u00e0 ma\u00eetriser le fran\u00e7ais en 4 semaines ?")
h = h.replace("R\u00e9servez votre formation en solo","R\u00e9servez Fran\u00e7ais Express")
h = h.replace("R\u00e9servez votre formation","R\u00e9servez Fran\u00e7ais Express")

# 2) Specific phrases with "cours solo" (BEFORE general "Cours solo" replacement)
h = h.replace("Boostez votre fran\u00e7ais avec les cours solo Cultulangues, la formule la plus flexible et la plus efficace pour atteindre rapidement vos objectifs linguistiques. Choisissez exactement ce dont vous avez besoin gr\u00e2ce \u00e0 nos cours \u00e0 la carte : parcours complet, Cap sur l\u0027oral, pr\u00e9paration TCF, module de r\u00e9vision, tests fonction publique, simulations d\u0027examen, maintien du niveau.","Boostez votre fran\u00e7ais avec Fran\u00e7ais Express, le programme intensif con\u00e7u pour vous faire progresser rapidement. 60 heures de formation structur\u00e9e : compr\u00e9hension orale, expression \u00e9crite, grammaire appliqu\u00e9e et mise en situation r\u00e9elle. Rapport de progression et certificat inclus.")
h = h.replace("Boostez votre fran\u00e7ais avec les cours solo Cultulangues, la formule la plus flexible et la plus efficace pour atteindre rapidement vos objectifs linguistiques. Choisissez exactement ce dont vous avez besoin gr\u00e2ce \u00e0 nos cours \u00e0 la carte :","Boostez votre fran\u00e7ais avec Fran\u00e7ais Express, le programme intensif en 4 semaines :")
h = h.replace("Boostez votre fran\u00e7ais avec les cours solo Cultulangues, la formule la plus flexible et la plus efficace pour atteindre rapidement vos objectifs linguistiques.","Boostez votre fran\u00e7ais avec Fran\u00e7ais Express \u2013 4 semaines intensives, 60 h, 5 max/groupe.")
h = h.replace("Boostez votre fran\u00e7ais avec les cours solo Cultulangues.","Boostez votre fran\u00e7ais avec Fran\u00e7ais Express.")
h = h.replace("des cours solo Cultulangues","du programme Fran\u00e7ais Express")
h = h.replace("les cours solo Cultulangues","le programme Fran\u00e7ais Express")
h = h.replace("avec les cours solo Cultulangues","avec Fran\u00e7ais Express")
h = h.replace("cours solo Cultulangues","programme Fran\u00e7ais Express")
# "Cours solo flexibles" -> specific audience phrasing
h = h.replace("Cours solo flexibles en journ\u00e9e, soir\u00e9e ou week-end.","Cours en soir\u00e9e (17\u201320h) compatibles avec votre horaire de travail.")
h = h.replace("cours solo flexibles en journ\u00e9e, soir\u00e9e ou week-end.","cours en soir\u00e9e (17\u201320h) compatibles avec votre horaire.")

# 3) Service name (covers most occurrences)
h = h.replace("Formation en <span style=\"color:#FA4E30;\">solo</span>","Fran\u00e7ais <span style=\"color:#FA4E30;\">Express</span>")
h = h.replace('Formation en <span class="gradient" style="background:linear-gradient(135deg,#FA4E30,#FC9705);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">solo</span>','Fran\u00e7ais <span class="gradient" style="background:linear-gradient(135deg,#FA4E30,#FC9705);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Express</span>')
h = h.replace("Formation en solo","Fran\u00e7ais Express")
h = h.replace("FORMATION EN SOLO","FRAN\u00c7AIS EXPRESS")
h = h.replace("formation en solo","Fran\u00e7ais Express")
h = h.replace("formation solo","Fran\u00e7ais Express")
h = h.replace("Solo<br><span style=\"color:#FA4E30;\">Power</span>","Express<br><span style=\"color:#FA4E30;\">Intensif</span>")
h = h.replace("Cultulangues \u00b7 Formation en solo","Cultulangues \u00b7 Programme intensif")
# Remaining "Cours solo" / "cours solo" in contexts where replacement is safe
h = h.replace("Cours solo","Fran\u00e7ais Express")
h = h.replace("cours solo","Fran\u00e7ais Express")

# 4) Hero titles (BEFORE hero subtitle replacements)
h = h.replace("La formation la plus <span style=\"background:linear-gradient(135deg,#FA4E30,#FC9705);-webkit-background-clip:text;-webkit-text-fill-color:transparent;\">flexible</span>","Fran\u00e7ais Express \u2013 4 semaines <span style=\"background:linear-gradient(135deg,#FA4E30,#FC9705);-webkit-background-clip:text;-webkit-text-fill-color:transparent;\">intensives</span>")
h = h.replace("La formation la plus flexible et la plus efficace pour atteindre rapidement vos objectifs linguistiques.","Fran\u00e7ais Express \u2013 4 semaines pour ma\u00eetriser le fran\u00e7ais en petit groupe intensif.")
h = h.replace("La formation la plus flexible","Fran\u00e7ais Express \u2013 4 semaines intensives")
h = h.replace("la formation la plus flexible et la plus efficace pour atteindre rapidement vos objectifs linguistiques.","le programme intensif Express \u2013 4 semaines, 60 h, 5 max/groupe.")
h = h.replace("la formation la plus flexible et la plus efficace pour atteindre rapidement vos objectifs linguistiques","le programme intensif Express \u2013 4 semaines, 60 h, 5 max/groupe")

# 5) Hero subtitles
h = h.replace("Atteignez rapidement vos objectifs linguistiques avec un programme 100% personnalis\u00e9. Jours et horaires selon vos disponibilit\u00e9s.","Ma\u00eetrisez le fran\u00e7ais en 4 semaines avec un programme intensif en petit groupe. Lun\u2013Ven 17\u201320h.")
h = h.replace("Atteignez rapidement vos objectifs linguistiques avec un programme 100% personnalis\u00e9.","Ma\u00eetrisez le fran\u00e7ais en 4 semaines. 60 h, 5 max/groupe, Lun\u2013Ven 17\u201320h.")
h = h.replace("Atteignez rapidement vos objectifs linguistiques avec un programme 100% personnalis\u00e9","Ma\u00eetrisez le fran\u00e7ais en 4 semaines. 60 h, 5 max/groupe, Lun\u2013Ven 17\u201320h.")
h = h.replace("Atteignez vos objectifs linguistiques avec un programme 100% personnalis\u00e9.","Ma\u00eetrisez le fran\u00e7ais en 4 semaines. 60 h, 5 max/groupe, Lun\u2013Ven 17\u201320h.")
h = h.replace("La formule la plus flexible pour atteindre vos objectifs linguistiques. 100% personnalis\u00e9e.","4 semaines intensives \u2013 60 h encadr\u00e9es, 5 max/groupe, Lun\u2013Ven 17\u201320h. 600 $ tout compris.")
h = h.replace("La formule la plus flexible pour atteindre vos objectifs linguistiques. 100% personnalis\u00e9e","4 semaines intensives \u2013 60 h encadr\u00e9es, 5 max/groupe, Lun\u2013Ven 17\u201320h. 600 $ tout compris.")
h = h.replace("La formule la plus flexible et la plus efficace pour atteindre rapidement vos objectifs linguistiques.","Le programme intensif Fran\u00e7ais Express \u2013 4 semaines pour des r\u00e9sultats concrets.")
h = h.replace("Des cours individuels flexibles pour atteindre vos objectifs linguistiques.","Un programme intensif de 4 semaines pour des r\u00e9sultats rapides. Lun\u2013Ven 17\u201320h.")
h = h.replace("La formule la plus flexible et la plus efficace pour atteindre vos objectifs linguistiques.","Fran\u00e7ais Express \u2013 4 semaines intensives, 60 h, 5 max/groupe.")
h = h.replace("La formule la plus flexible pour atteindre vos objectifs linguistiques.","Fran\u00e7ais Express \u2013 4 semaines intensives, 60 h, 5 max/groupe.")

# 6) Tags (keep short and safe)
h = h.replace("100% personnalis\u00e9","4 semaines")
h = h.replace("5\u201320 heures","60 heures")
h = h.replace("5\u201320h","60 h")
h = h.replace("38\u201345 $/h","600 $")
h = h.replace("Flexible","Lun\u2013Ven 17\u201320h")

# 7) Trust strip
h = h.replace("\u00c9valuation initiale offerte","\u00c9valuation de placement incluse")
h = h.replace("Sans engagement","Certificat inclus")
h = h.replace("Annulation flexible","4 semaines intensives")
h = h.replace("Accompagnement d\u00e9di\u00e9","5 max par groupe")

# 8) Feature section headings
h = h.replace("Pourquoi choisir la formule solo ?","Pourquoi choisir Fran\u00e7ais Express ?")
h = h.replace("Pourquoi la formule solo ?","Pourquoi Fran\u00e7ais Express ?")
h = h.replace("Choisir la formule solo ?","Choisir Fran\u00e7ais Express ?")
h = h.replace("la formule solo ?","Fran\u00e7ais Express ?")
h = h.replace("la formule <span style=\"background:linear-gradient(135deg,#FA4E30,#FC9705);-webkit-background-clip:text;-webkit-text-fill-color:transparent;\">solo</span>","Fran\u00e7ais <span style=\"background:linear-gradient(135deg,#FA4E30,#FC9705);-webkit-background-clip:text;-webkit-text-fill-color:transparent;\">Express</span>")
h = h.replace("la formule solo","Fran\u00e7ais Express")

# 9) Feature items (specific before general)
h = h.replace("Flexibilit\u00e9 totale","4 semaines intensives")
h = h.replace("Personnalisation compl\u00e8te","60 heures encadr\u00e9es")
h = h.replace("Forfaits 5h \u00e0 20h","Petits groupes (5 max)")
h = h.replace("Forfaits 5h\u201320h","Petits groupes (5 max)")
h = h.replace("R\u00e9sultats concrets","R\u00e9sultats garantis")
# Individual feature items after compound ones
h = h.replace("Personnalisation","60 heures encadr\u00e9es")

# Feature descriptions
h = h.replace("Vous choisissez vos jours et horaires selon votre emploi du temps.","15 h/semaine avec des formateurs experts FLE.")
h = h.replace("Jours et horaires selon vos disponibilit\u00e9s.","15 h/semaine avec des formateurs experts.")
h = h.replace("Vous choisissez vos jours et horaires.","15 h/semaine avec des formateurs experts.")
h = h.replace("Programme sur mesure adapt\u00e9 \u00e0 votre niveau et vos objectifs.","Programme structur\u00e9 couvrant les 4 comp\u00e9tences linguistiques.")
h = h.replace("Programme sur mesure adapt\u00e9 \u00e0 votre niveau.","Programme structur\u00e9 couvrant les 4 comp\u00e9tences.")
h = h.replace("Programme sur mesure.","Programme structur\u00e9.")
h = h.replace("Choisissez le forfait qui vous correspond. Tarif d\u00e9gressif.","Maximum 5 apprenants pour un suivi personnalis\u00e9.")
h = h.replace("Choisissez le forfait qui vous correspond. Tarif d\u00e9gressif","Maximum 5 apprenants pour un suivi personnalis\u00e9.")
h = h.replace("Tarif d\u00e9gressif selon le volume.","Maximum 5 apprenants.")
h = h.replace("Progression rapide et mesurable d\u00e8s les premi\u00e8res s\u00e9ances.","Progression mesurable d\u00e8s la 1re semaine.")
h = h.replace("Progression rapide et mesurable d\u00e8s les premi\u00e8res s\u00e9ances","Progression mesurable d\u00e8s la 1re semaine.")
h = h.replace("Progression d\u00e8s les premi\u00e8res s\u00e9ances.","Progression mesurable d\u00e8s la 1re semaine.")
h = h.replace("Progression rapide.","Progression d\u00e8s la 1re semaine.")

# 10) Audience
h = h.replace("Cours solo flexibles en journ\u00e9e, soir\u00e9e ou week-end.","Cours en soir\u00e9e (17\u201320h) compatibles avec votre horaire de travail.")  # déjà fait plus haut, mais safe
h = h.replace("Cours flexibles adapt\u00e9s \u00e0 votre emploi du temps.","Horaire fixe Lun\u2013Ven 17\u201320h, id\u00e9al apr\u00e8s le travail.")
h = h.replace("Programme intensif pour TCF, TEF, DELF/DALF.","Pr\u00e9paration intensive TCF, TEF, DELF/DALF en 4 semaines.")
h = h.replace("Le 1-to-1 est la solution la plus efficace pour progresser.","Int\u00e9gration linguistique acc\u00e9l\u00e9r\u00e9e en petit groupe.")
h = h.replace("Professionnels, candidats examens, apprenants ambitieux","Professionnels en activit\u00e9, candidats aux examens, nouveaux arrivants")

# 11) Description checklist items
h = h.replace("Parcours linguistique complet","Compr\u00e9hension et expression orale")
h = h.replace("Programme Cap sur l\u0027oral","Grammaire et vocabulaire appliqu\u00e9s")
h = h.replace("Pr\u00e9paration TCF","Mises en situation r\u00e9elles")
h = h.replace("Module de r\u00e9vision","Rapport de progression inclus")
h = h.replace("Tests fonction publique","Certificat de fin de session")
h = h.replace("Simulations d\u0027examen","Exercices intensifs quotidiens")
h = h.replace("Maintien du niveau","Suivi personnalis\u00e9 en groupe")

# 12) Pricing section
h = h.replace("Choisissez le nombre d\u0027heures qui vous convient. Tarifs d\u00e9gressifs selon le forfait.","Forfait unique \u00e0 600 $ \u2013 tout compris.")
h = h.replace("Tous les forfaits incluent une \u00e9valuation initiale et un programme sur mesure.","\u00c9valuation de placement, 60 h d\u0027enseignement et rapport final inclus \u00e0 600 $.")
h = h.replace("Tous les forfaits incluent une \u00e9valuation initiale.","\u00c9valuation de placement incluse. 600 $ tout compris.")

# 13) How it works
h = h.replace("Choisissez votre forfait","Choisissez votre session")
h = h.replace("5h, 10h, 15h ou 20h selon vos besoins.","Consultez le calendrier et r\u00e9servez votre mois.")
h = h.replace("5h, 10h, 15h ou 20h.","Consultez le calendrier.")
h = h.replace("Rencontre p\u00e9dagogique","\u00c9valuation de placement")
h = h.replace("Remplissez la fiche d\u0027inscription.","Remplissez la fiche d\u0027inscription pour \u00e9valuer votre niveau.")
h = h.replace("Inscription et conseils.","Inscription et \u00e9valuation de niveau.")
h = h.replace("R\u00e9servez vos s\u00e9ances","D\u00e9marrez le programme")
h = h.replace("Choisissez jours et horaires.","Lun\u2013Ven 17\u201320h pendant 4 semaines.")
h = h.replace("Jours et horaires flexibles.","Lun\u2013Ven 17\u201320h pendant 4 semaines.")

# 14) Outcomes (specific before general)
h = h.replace("Progression acc\u00e9l\u00e9r\u00e9e","R\u00e9sultats visibles d\u00e8s la 1re semaine")
h = h.replace("Objectifs cibl\u00e9s","Niveau sup\u00e9rieur garanti")
h = h.replace("Confiance renforc\u00e9e","Certificat de fin de session")
# Replace "Flexibilit\u00e9" only as outcome (NOT in features - already done)
# We need a safer approach. Let's do it by context.
# These are in outcome grid items/cards, so they're typically standalone short words
h = h.replace("\u00ab Le format solo est exactement ce qu\u0027il me fallait. Flexibilit\u00e9, personnalisation et r\u00e9sultats rapides. \u00bb","\u00ab Le format intensif m\u0027a permis de passer de A1 \u00e0 A2 en seulement 4 semaines. Incroyable ! \u00bb")
h = h.replace("\u00ab Le format solo est exactement ce qu\u0027il me fallait. Flexibilit\u00e9, personnalisation et r\u00e9sultats rapides. \u00bb","\u00ab Le format intensif m\u0027a permis de passer de A1 \u00e0 A2 en seulement 4 semaines. Incroyable ! \u00bb")

# 15) Testimonials
h = h.replace("\u00ab Le format solo est exactement ce qu\u0027il me fallait. Flexibilit\u00e9, personnalisation et r\u00e9sultats rapides. \u00bb","\u00ab Le format intensif m\u0027a permis de passer de A1 \u00e0 A2 en seulement 4 semaines. Incroyable ! \u00bb")
h = h.replace("\u00ab Le format solo est exactement ce qu\u0027il me fallait. \u00bb","\u00ab Le format intensif m\u0027a permis de passer de A1 \u00e0 A2 en seulement 4 semaines. \u00bb")
h = h.replace("\u00ab J\u0027ai pr\u00e9par\u00e9 mon TCF gr\u00e2ce au forfait 15h. J\u0027ai obtenu le niveau B2 du premier coup. \u00bb","\u00ab J\u0027ai pr\u00e9par\u00e9 mon TCF et obtenu B2 gr\u00e2ce \u00e0 ce programme intensif. \u00bb")
h = h.replace("\u00ab J\u0027ai obtenu le niveau B2 du premier coup. \u00bb","\u00ab J\u0027ai pr\u00e9par\u00e9 mon TCF et obtenu B2 gr\u00e2ce \u00e0 ce programme intensif. \u00bb")
h = h.replace("\u00ab J\u0027ai obtenu mon B2 du premier coup. \u00bb","\u00ab J\u0027ai pr\u00e9par\u00e9 mon TCF et obtenu B2 gr\u00e2ce \u00e0 ce programme intensif. \u00bb")
h = h.replace("\u00ab J\u0027ai obtenu le B2 du premier coup. \u00bb","\u00ab J\u0027ai pr\u00e9par\u00e9 mon TCF et obtenu B2 gr\u00e2ce \u00e0 ce programme intensif. \u00bb")
h = h.replace("\u00ab Les cours du soir apr\u00e8s le travail, c\u0027\u00e9tait id\u00e9al. Mon formateur a su cibler mes points faibles. \u00bb","\u00ab Les cours de 17h \u00e0 20h sont parfaits pour les professionnels. Formateurs comp\u00e9tents. \u00bb")
h = h.replace("\u00ab Mon formateur a su cibler mes points faibles. \u00bb","\u00ab Les cours en soir\u00e9e sont parfaits pour les professionnels. \u00bb")
h = h.replace("\u00ab Mon formateur a cibl\u00e9 mes points faibles. \u00bb","\u00ab Les cours en soir\u00e9e sont parfaits pour les professionnels. \u00bb")
h = h.replace("\u00ab Flexibilit\u00e9, personnalisation et r\u00e9sultats rapides. \u00bb","\u00ab 4 semaines seulement et j\u0027ai vu une vraie diff\u00e9rence. Format intensif tr\u00e8s efficace. \u00bb")
h = h.replace("\u00ab Flexibilit\u00e9, personnalisation et r\u00e9sultats. \u00bb","\u00ab 4 semaines intensives qui changent la donne. Format express tr\u00e8s efficace. \u00bb")
h = h.replace("\u00ab Flexibilit\u00e9, personnalisation et r\u00e9sultats. \u00bb","\u00ab 4 semaines intensives qui changent la donne. Format express tr\u00e8s efficace. \u00bb")

# Testimonial names & roles
h = h.replace("Sophie M.","Marie\u2011Claire D.")
h = h.replace("Ahmed K.","Jean\u2011Luc T.")
h = h.replace("Li W.","Fatima Z.")
h = h.replace("Professionnelle en activit\u00e9","Niveau d\u00e9butant")
h = h.replace("Professionnelle","Niveau d\u00e9butant")
h = h.replace("Candidat TCF","Professionnel")
h = h.replace("Niveau interm\u00e9diaire","Candidat TCF")

# 16) FAQ
h = h.replace("Quelle est la dur\u00e9e de chaque s\u00e9ance ?","Quand d\u00e9butent les prochaines sessions ?")
h = h.replace("Puis-je changer de forfait ?","Quels sont les pr\u00e9requis ?")
h = h.replace("Comment fonctionne l\u0027\u00e9valuation initiale ?","Y a-t-il une \u00e9valuation initiale ?")
h = h.replace("Cours en ligne ou pr\u00e9sentiel ?","Cours en ligne ou en pr\u00e9sentiel ?")
h = h.replace("Dur\u00e9e de chaque s\u00e9ance ?","Quand d\u00e9butent les sessions ?")
h = h.replace("Changer de forfait ?","Quels sont les pr\u00e9requis ?")
h = h.replace("\u00c9valuation initiale ?","Y a-t-il une \u00e9valuation ?")
h = h.replace("En ligne ou pr\u00e9sentiel ?","Cours en ligne ou en pr\u00e9sentiel ?")
h = h.replace("Dur\u00e9e des s\u00e9ances ?","Quand d\u00e9butent les sessions ?")

# 17) Concept 10 specific
h = h.replace("Pr\u00eat \u00e0 passer \u00e0 la vitesse sup\u00e9rieure ?","Pr\u00eat \u00e0 parler fran\u00e7ais en 4 semaines ?")

# 18) Miscellaneous fixes
h = h.replace("Niveau de confiance","Progression moyenne")
h = h.replace("96%","+85%")
h = h.replace("de nos apprenants satisfaits","de nos apprenants am\u00e9liorent leur niveau")

with open('detail-concepts-express.html','w',encoding='utf-8') as f:
    f.write(h)
    f.write('\n<!-- G\u00e9n\u00e9r\u00e9 par gen-express-demo.py le 30/07/2026 -->\n')

print("Done: detail-concepts-express.html regenerated")
