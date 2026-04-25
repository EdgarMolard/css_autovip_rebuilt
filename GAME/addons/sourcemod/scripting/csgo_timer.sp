#pragma semicolon 1
#pragma newdecls required

#include <sourcemod>
#include <sdktools>

#define PLUGIN_NAME "CSGO: Timer"
#define PLUGIN_DESC	"CSGO: Timer"
#define PLUGIN_AUTHOR	"Had3s99"
#define PLUGIN_VERSION	"1.0"
#define PLUGIN_URL	"lastfate.fr"

int Time[MAXPLAYERS + 1] = 0;

int OldTime[MAXPLAYERS + 1];
int NewTime[MAXPLAYERS + 1];

Database Dbase = null;

public Plugin myinfo = {
	name = PLUGIN_NAME, 
	description = PLUGIN_DESC, 
	author = PLUGIN_AUTHOR, 
	version = PLUGIN_VERSION, 
	url = PLUGIN_URL
};

public void OnPluginStart() {
	InitDB();
}

public void OnClientPutInServer(int client) {
	if(IsClientValid(client)) {
		CreateTimer(1.0, CompteSql, client);
		OldTime[client] = GetTime();
	}
}

public void OnMapEnd() {
	for(int i = 0; i < MaxClients; i++) {
		if(IsClientValid(i)) {
			SqlSave(i);
		}
	}
}

public void OnClientDisconnect(int client) {
	if(IsClientValid(client)) {
		SqlSave(client);
	}
}

stock bool IsClientValid(int client) {
	if(client > 0 && client <= MaxClients && IsClientConnected(client) && IsClientInGame(client) && !IsFakeClient(client)) {
			return true;
	}
	return false;
}

public void InitDB() {
	char sError[255];
	Dbase = SQL_Connect("Timer", true, sError, sizeof(sError));
	if (Dbase == null) {
		CreateTimer(1.0, Timer_DBRetry, _, TIMER_FLAG_NO_MAPCHANGE);
	}
}

public Action Timer_DBRetry(Handle timer) {
	if (Dbase == null) {
		InitDB();
	}
}

public Action CompteSql(Handle Timer, any client) {
	if (IsClientInGame(client)) {
		char SteamId[64];
		GetClientAuthId(client, AuthId_Steam2, SteamId, sizeof(SteamId));
		
		if(StrEqual(SteamId, "")) CreateTimer(1.0, CompteSql, client);
		else DbClient(client);
	}
}

public void DbClient(int client) {
	DBResultSet hQuery;
	char sQuery[256];
	char SteamId[64];
	
	GetClientAuthId(client, AuthId_Steam2, SteamId, sizeof(SteamId));
	
	Format(sQuery, sizeof(sQuery), "SELECT * FROM Time_Players WHERE SteamId = '%s'", SteamId);
	hQuery = SQL_Query(Dbase, sQuery);
	
	if (hQuery == null) {
		char error[255];
		SQL_GetError(Dbase, error, sizeof(error));
		PrintToServer("Failed to query (error: %s)", error);
	}
	else {
		if (hQuery) {
			if (!SQL_FetchRow(hQuery)) {
				CreerCompteSql(client);
			}
			else {
				Time[client] = SQL_FetchInt(hQuery, 2);
			}
		}
		delete hQuery;
	}
}

public void CreerCompteSql(int client) {
	char sQuery[256];
	
	char Pseudo[64];
	char SteamId[64];
	
	GetClientName(client, Pseudo, sizeof(Pseudo));
	GetClientAuthId(client, AuthId_Steam2, SteamId, sizeof(SteamId));

	Format(sQuery, sizeof(sQuery), "INSERT INTO Time_Players (Pseudo, Time, SteamId) VALUES ('%s', '0', '%s')", Pseudo, SteamId);
	SQL_Query(Dbase, sQuery);
	
	
	Time[client] = 0;
}

public void SqlSave(int client) {
	DBResultSet hQuery;
	char sQuery[256];

	char Pseudo[64];
	char SteamId[64];

	GetClientName(client, Pseudo, sizeof(Pseudo));
	GetClientAuthId(client, AuthId_Steam2, SteamId, sizeof(SteamId));
	
	Format(sQuery, sizeof(sQuery), "SELECT * FROM Time_Players WHERE SteamId = '%s'", SteamId);
	hQuery = SQL_Query(Dbase, sQuery);
	
	if (hQuery == null) {
		char error[255];
		SQL_GetError(Dbase, error, sizeof(error));
		PrintToServer("Failed to query (error: %s)", error);
	} 
	else {
		if (hQuery) {
			if (!SQL_FetchRow(hQuery)) {
				CreerCompteSql(client);
			}
			else {
				NewTime[client] = GetTime();
				Time[client] = NewTime[client] - OldTime[client] + Time[client];
				OldTime[client] = GetTime();
				
				Format(sQuery, sizeof(sQuery), "UPDATE Time_Players SET Pseudo = '%s', Time = '%i' WHERE SteamId = '%s' LIMIT 1", Pseudo, Time[client], SteamId);
				SQL_Query(Dbase, sQuery);
			}
		}
		delete hQuery;
	}
}