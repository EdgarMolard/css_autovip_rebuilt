public Action Command_Inscription(int client, int args) {
	if (IsClientInGame(client)) QueryClientConVar(client, "cl_disablehtmlmotd", view_as<ConVarQueryFinished> (ClientConVar), client); // On vérifie si l'utilisateur peut afficher la page ou non
}

public Action Command_InscriptionAdmin(int client, int args) {
	if (IsClientInGame(client) && GetConVarBool(g_AdminActivated)) QueryClientConVar(client, "cl_disablehtmlmotd", view_as<ConVarQueryFinished> (ClientConVarAdmin), client); // On vérifie si l'utilisateur peut afficher la page ou non, uniquement si l'Admin est actif sur le serveur	
}

public void ClientConVar(QueryCookie cookie, int client, ConVarQueryResult result, const char[]cvarName, const char[] cvarValue)
{
	if(!StrEqual(cvarValue, "0"))
		CPrintToChat(client, "%s Veuillez taper {darkblue}cl_disablehtmlmotd 0 {default}dans votre console.", LOGO); // Si le MOTD est désactivé par l'utilisateur, on lui affiche le message d'erreur, uniquement si l'Admin est actif sur le serveur
	else
	{
		char url[320];
		char sBuffer[256];
		char steamid[64];
		char name[64];
		
		GetClientAuthId(client, AuthId_Steam2, steamid, sizeof(steamid));
		GetClientName(client, name, sizeof(name));
		GetGameFolderName(gamedir, sizeof(gamedir));
		
		GetConVarString(g_SiteURL, sBuffer, sizeof(sBuffer));
		Format(url, sizeof(url), "%s/index.php?p=register&psdo=%s&steam_id=%s", sBuffer, name, steamid);  // Lien pris dans les CVAR, y est ajouté la fin du lien remplissant le formulaire automatiquement
		
		if(strcmp(gamedir, "cstrike") == 0)
		{
			ShowMOTDPanel(client, "Autovip", url, 2);
		}
	}
}

public void ClientConVarAdmin(QueryCookie cookie, int client, ConVarQueryResult result, const char[] cvarName, const char[] cvarValue) {
	if(!StrEqual(cvarValue, "0"))
		CPrintToChat(client, "%s Veuillez taper {darkblue}cl_disablehtmlmotd 0 {default}dans votre console.", LOGO); // Si le MOTD est désactivé par l'utilisateur, on lui affiche le message d'erreur, uniquement si l'Admin est actif sur le serveur
	else {
		char url[320];
		char sBuffer[256];
		char steamid[64];
		char name[64];
		GetClientAuthId(client, AuthId_Steam2, steamid, sizeof(steamid));
		
		GetClientName(client,name,sizeof(name));
		
		GetConVarString(g_SiteURL, sBuffer, sizeof(sBuffer));
		Format(url, sizeof(url), "%s/index.php?p=register&psdo=%s&steam_id=%s", sBuffer, name, steamid);  // Lien pris dans les CVAR, y est ajouté la fin du lien remplissant le formulaire automatiquement
		
		if(strcmp(gamedir, "cstrike") == 0) {
			ShowMOTDPanel(client, "AutoAdmin", url, 2);
		}
	}
}