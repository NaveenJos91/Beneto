// ------------------------------------------------------------
// Script de démonstration NoSQL (MongoDB) pour Bénéto
// Cas d'usage : journalisation des signalements d'annonces.
// Les données du coeur métier restent en MySQL (relationnel) ;
// MongoDB stocke des documents semi-structurés ajoutés en continu.
// ------------------------------------------------------------

db = db.getSiblingDB('beneto');

// On repart d'une base propre pour la démonstration
db.signalements.deleteMany({});

// --- INSERTION de documents (signalements) ---
db.signalements.insertMany([
  { annonceId: 12, motif: "Contenu inapproprié",   auteurEmail: "apprenant1@example.com", date: new Date() },
  { annonceId: 7,  motif: "Annonce en double",      auteurEmail: "apprenant2@example.com", date: new Date() },
  { annonceId: 12, motif: "Coordonnées suspectes",  auteurEmail: "apprenant3@example.com", date: new Date() }
]);

print("=== Nombre total de signalements : " + db.signalements.countDocuments() + " ===");

// --- LECTURE : tous les signalements ---
print("\n--- Tous les signalements ---");
printjson(db.signalements.find().toArray());

// --- REQUETE FILTREE : signalements de l'annonce n°12 ---
print("\n--- Signalements concernant l'annonce 12 ---");
printjson(db.signalements.find({ annonceId: 12 }).toArray());