<?php

	// ...<1000 : erreurs sp�cifiques � une page
	const ERR_NOTFOUND = 404;
	const ERR_404 = "Une resource n'a pas �t� trouv�e.";
	
	const ERR_UNKNOWN = 500;
	const ERR_500 = "Erreur g�n�rique.";
	
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
	const ERR_1003 = "Cette action a �t� d�sactiv�e.";
	
	const ERR_SQL = 1004;
	const ERR_1004 = "La base de donn�e n'a pas fourni le r�sultat attendu.";

    const ERR_NOT_IMPLEMENTED = 1005;
    const ERR_1005 = "Fonction non-impl�ment�e.";
	
	// 11xx : erreurs de connexion
    const ERR_NOT_CONNECTED = 1101;
    const ERR_1101 = "La connexion a �chou�e.";
	
	// 20xx : erreurs de s�curit�
    const ERR_RIGHTS = 2000;
	const ERR_2000 = "L'utilisateur en cours n'avait pas assez de droits pour effectuer cette action.";
	
	const ERR_ILLOGICAL = 2001;
	const ERR_2001 = "Cette action n'est pas logique.";
	
	const ERR_BRUTEFORCEATTACK = 2002;
	const ERR_2002 = "Un nombre beaucoup trop important de tentatives infructueuses de connexions a �t� d�tect�. Le site a �t� d�sactiv� par mesure de s�curit�";
?>