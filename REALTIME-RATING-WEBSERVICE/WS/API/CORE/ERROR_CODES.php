<?php

	// ...<1000 : erreurs spécifiques à une page
	const ERR_NOTFOUND = 404;
	const ERR_404 = "Une resource n'a pas été trouvée.";
	
	const ERR_UNKNOWN = 500;
	const ERR_500 = "Erreur générique.";
	
	const ERR_PHP = 666;
	const ERR_666 = "Erreur native PHP.";

	// 10xx : erreurs communes
	const ERR_ARGS = 1000;
	const ERR_1000 = "Arguments invalides.";
	
	const ERR_INVALID = 1001;
	const ERR_1001 = "Action invalide.";
	
	const ERR_UNABLE = 1002;
	const ERR_1002 = "Impossible d'effectuer l'action.";
	
	const ERR_DISABLED = 1003;
	const ERR_1003 = "Cette action a été désactivée.";
	
	const ERR_SQL = 1004;
	const ERR_1004 = "La base de donnée n'a pas fourni le résultat attendu.";

    const ERR_NOT_IMPLEMENTED = 1005;
    const ERR_1005 = "Fonction non-implémentée.";
	
	// 11xx : erreurs de connexion
    const ERR_NOT_CONNECTED = 1101;
    const ERR_1101 = "La connexion a échouée.";
	
	// 20xx : erreurs de sécurité
    const ERR_RIGHTS = 2000;
	const ERR_2000 = "L'utilisateur en cours n'avait pas assez de droits pour effectuer cette action.";
	
	const ERR_ILLOGICAL = 2001;
	const ERR_2001 = "Cette action n'est pas logique.";
	
	const ERR_BRUTEFORCEATTACK = 2002;
	const ERR_2002 = "Un nombre beaucoup trop important de tentatives infructueuses de connexions a été détecté. Le site a été désactivé par mesure de sécurité";
?>