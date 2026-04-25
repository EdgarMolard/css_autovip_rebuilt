public Action Command_Password(int client, int args) {
	if (connexion) {
		char steamid[32];
		char user[64];
		char Prefix[12];
		char query[450];
		
		GetConVarString(g_PrefixDb, Prefix, sizeof(Prefix));		
		GetClientAuthId(client, AuthId_Steam2, steamid, sizeof(steamid));
		GetClientName(client,user, sizeof(user));
		
		Format(query, sizeof(query), "SELECT id FROM `%s_users` WHERE steam_id='%s' LIMIT 0,1", Prefix, steamid);
		Handle hQuery = SQL_Query(db, query);
		if (SQL_FetchRow(hQuery)) {
			char Arg[256];
			GetCmdArgString(Arg, sizeof(Arg));
			StripQuotes(Arg);
			TrimString(Arg);		
		
			if(strlen(Arg) < 1) {
				CPrintToChat(client, "{darkred}[{darkblue}Password{darkred}] {default} Tapez !pw <MDP> {darkblue}pour le changer sur l'AutoVIP !");
				return Plugin_Handled;
			}
			else if(strlen(Arg) < 6 || strlen(Arg) > 32) {
				CPrintToChat(client, "{darkred}[{darkblue}Password{darkred}] {default} Votre mot de passe doit contenir de {darkred}6 à 32 caractères {default}!");
				return Plugin_Handled;
			}
			else {
				CPrintToChat(client, "{darkred}[{darkblue}Password{darkred}] {default} Votre nouveau mot de passe est : {darkblue}%s {default}!", Arg);
				char Password[200];
				Crypt_MD5(Arg, Password, 200);
				char query2[450];	
				Format(query2, sizeof(query2), "UPDATE `%s_users` SET password='%s' WHERE steam_id='%s'", Prefix, Password, steamid);
				SQL_Query(db, query2);
			}
		}
		else {
			CPrintToChat(client, "{darkred}[{darkblue}Password{darkred}] {default} Vous devez avoir un compte AutoVIP !");
			return Plugin_Handled;		
		}
	}
	
	return Plugin_Continue;
}