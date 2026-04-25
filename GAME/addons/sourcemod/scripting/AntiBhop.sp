#include <devzones>
#include <morecolors>
#include <sdktools>

#pragma newdecls required
#pragma semicolon 1

#define PLUGIN_NAME "AntiBhop"
#define PLUGIN_AUTHOR "Had3s99"
#define PLUGIN_DESC "Pour éviter les prébhops"
#define PLUGIN_VERSION "1.0"
#define PLUGIN_URL "lastfate.fr"

bool CanBhop[MAXPLAYERS + 1];
bool InBhop[MAXPLAYERS + 1];

Handle g_hVitesseMax = INVALID_HANDLE;

public Plugin myinfo =  {
	name = PLUGIN_NAME,
	author = PLUGIN_AUTHOR,
	description = PLUGIN_DESC,
	version = PLUGIN_VERSION,
	url = PLUGIN_URL
};

public void OnPluginStart() {
	g_hVitesseMax =  CreateConVar("sm_vitesse_max", "285.0", "La vitesse max pour éviter le prébhop");
	
	AutoExecConfig(true, "sm_antibhop");
	
	HookEvent("player_death", Gestion_Mort);
	HookEvent("player_jump", Gestion_Bhop);
}

public Action Gestion_Mort(Handle event, const char[] name, bool dontBroadcast) {
	int victim = GetClientOfUserId(GetEventInt(event, "userid"));
	
	if(!CanBhop[victim])
		CanBhop[victim] = true;
}

public int Zone_OnClientEntry(int client, char[] zone) {
	if(StrContains(zone, "bhop", false) != -1) {
		CanBhop[client] = false;
	}
}

public int Zone_OnClientLeave(int client, char[] zone) {
	if(StrContains(zone, "bhop", false) != -1) {
		CanBhop[client] = true;
	}
}

public Action OnPlayerRunCmd(int client, int &buttons, int &impulse, float vel[3], float angles[3], int &weapon) {
	if (InBhop[client]) {
		return Plugin_Handled;
	}
		
	return Plugin_Continue;
}
		

public Action Gestion_Bhop(Handle event, const char[] name, bool dontBroadcast) {
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	
	float l_fVelocity[3], l_fVitesse;
	
	GetEntPropVector(client, Prop_Data, "m_vecVelocity", l_fVelocity);
	l_fVitesse = SquareRoot(Pow(l_fVelocity[0],2.0)+Pow(l_fVelocity[1],2.0));

	//CPrintToChat(client, "[Debug] Vitesse : %.0f", l_fVitesse);
	
	if(!CanBhop[client] && l_fVitesse >= GetConVarFloat(g_hVitesseMax)) {
		CPrintToChat(client, "{red}Tu ne peux pas prébhop !");
		InBhop[client] = true;
		CreateTimer(1.0, StopBhop, client);
	}
}

public Action StopBhop(Handle Timer, any client) {
	InBhop[client] = false;
}