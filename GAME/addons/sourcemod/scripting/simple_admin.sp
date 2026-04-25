#include <sourcemod>
#include <sdktools>
#include <sdkhooks>
#include <multicolors>
#include <smlib>

native int IsVipAVie(int client);

#pragma newdecls required
#pragma semicolon 1

#define LOGO "{darkred}[{darkblue}VIP{darkred}]{default}"
#define VERSION "1.3b"

#include "simple_admin/var.sp"
#include "simple_admin/commande_droits.sp"
#include "simple_admin/fonctions.sp"
#include "simple_admin/commande_inscription.sp"
#include "simple_admin/commande_password.sp"

public Plugin myinfo = {
	name = "Admin Facile",
	author = "Steven",
	version = VERSION,
	description = "Système automatique d'ajout de vip et d'admin",
	url = "http://nsnf-clan.net/"
};

public APLRes AskPluginLoad2(Handle myself, bool late, char[] error, int err_max)
{
	RegPluginLibrary("Admin_Facile");

	CreateNative("isVipAVie", Native_IsVipAVie);
	MarkNativeAsOptional("IsVipAVie");
	
	return APLRes_Success;
}

public void OnClientPostAdminCheck(int client) {
	Droit(client);
}

public void OnRebuildAdminCache(AdminCachePart part) {
	if (part == AdminCache_Admins)
		for(int sup = 1; sup <= MaxClients; sup++)
			if (IsClientInGame(sup) && !IsFakeClient(sup))
				CreateTimer(0.1, RefreshDroit, sup);
}

public void OnPluginStart() {
	GetGameFolderName(gamedir, sizeof(gamedir));
	
	g_PrefixDb =  CreateConVar("sm_prefix_db", "af", "Préfixe de la BDD pour les Sites");
	g_SiteURL =  CreateConVar("sm_site_URL", "https://vip.lastfate.fr", "URL du Site VIP");
	g_AdminActivated = CreateConVar("sm_admin_on", "0", "AutoAdmin activé sur ce serveur ?", _, true, 0.0, true, 1.0);
	g_DroitUniqueAdmin = CreateConVar("sm_admin_unique", "0", "Droit Admin Unique Par Serveur", _, true, 0.0, true, 1.0);
	g_DroitUniqueVip = CreateConVar("sm_vip_unique", "0", "Droit Vip Unique Par Serveur", _, true, 0.0, true, 1.0);

	AutoExecConfig(true, "sm_admin_facile");

	RegConsoleCmd("sm_vip", Command_Inscription);
	RegConsoleCmd("sm_autoadmin", Command_InscriptionAdmin);
	RegConsoleCmd("sm_pw", Command_Password);
	
	HookEvent("round_end", OnRoundEnd);
	
	AddCommandListener(Say, "say");
	AddCommandListener(Say, "say_team");
}

public void OnMapStart() {
	ConnectDb();
}

public void OnMapEnd() {
	DisconnectDb();
}

public Action Say(int client, char []command, int args) {
	if (client > 0) {
		if (IsClientInGame(client) && !IsFakeClient(client)) {
			char Arg[256];
			GetCmdArgString(Arg, sizeof(Arg));
			StripQuotes(Arg);
			TrimString(Arg);
			
			if(Arg[0] == '!' && Arg[1] == 'p' && Arg[2] == 'w') {
				PrintToServer("[SAY] %N : %s", client, Arg);				
				return Plugin_Handled;
			}
		}
	}
	
	return Plugin_Continue;
}
	
public Action OnRoundEnd(Handle event, const char []name, bool dontBroadcast) {
	if (!connexion) {
		ConnectDb();
	
		ServerCommand("sm_reloadadmins");
	}
	else {
		for(int sup = 1; sup <= MaxClients; sup++)
			if (IsClientInGame(sup) && !IsFakeClient(sup))
				Droit(sup);
	}
}

public int Native_IsVipAVie(Handle plugin, int numParams) {
	int client = GetNativeCell(1);
	
	if (!IsClientInGame(client))
		return false;
	
	if (connexion) {
		char query[252];
		char steamid[32];
		char Prefix[12];
		
		GetConVarString(g_PrefixDb, Prefix, sizeof(Prefix));	
		GetClientAuthId(client, AuthId_Steam2, steamid, sizeof(steamid));

		Format(query, sizeof(query), "SELECT is_suspended, id FROM `%s_users` WHERE steam_id='%s' LIMIT 0,1", Prefix, steamid);	
		Handle hQuery = SQL_Query(db, query);
		if (SQL_FetchRow(hQuery)) {
			int Suspended, Id_User, l_iTemps = 12;
			Suspended = SQL_FetchInt(hQuery, 0);
			Id_User = SQL_FetchInt(hQuery, 1);
			hQuery = INVALID_HANDLE;

			if (Suspended == 1) return false;
			
			Format(query, sizeof(query), "SELECT date_fin FROM `%s_droits` WHERE user_id = '%i' LIMIT 0,1", Prefix, Id_User);	
			hQuery = SQL_Query(db, query);
			
			if (SQL_FetchRow(hQuery)) {
				l_iTemps = SQL_FetchInt(hQuery, 0);
				
				if (l_iTemps != 2147483647) return false;
			
				if (hQuery != INVALID_HANDLE) {
					if (CloseHandle(hQuery)) {
						hQuery = INVALID_HANDLE;
					}
				}
			}
		}
	}
	
	return true;
}
