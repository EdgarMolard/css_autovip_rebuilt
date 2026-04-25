public Action RefreshDroit(Handle timer, any sup) {
	if (IsClientInGame(sup))
		Droit(sup);
}

public void Droit(int client) {
	if (connexion) {
		int timestamp;
		int portserveur = GetConVarInt(FindConVar("hostport"));
		timestamp = GetTime();
		int clientFlags;
		clientFlags = GetUserFlagBits(client);
		char query[252];
		char steamid[32];
		char Prefix[12];

		GetConVarString(g_PrefixDb, Prefix, sizeof(Prefix));	
		GetClientAuthId(client, AuthId_Steam2, steamid, sizeof(steamid));
		
		if(!StrEqual(gamedir, "csgo")) {
			if(StrContains(steamid, "STEAM_0")) {
				ReplaceString(steamid, sizeof(steamid), "STEAM_0", "STEAM_1");
			}
		}
		
		Format(query, sizeof(query), "SELECT is_suspended, id FROM `%s_users` WHERE steam_id='%s' LIMIT 0,1", Prefix, steamid);	
		Handle hQuery = SQL_Query(db, query);
		if (SQL_FetchRow(hQuery)) {
			int Suspended, Id_User;
			Suspended = SQL_FetchInt(hQuery, 0);
			Id_User = SQL_FetchInt(hQuery, 1);

			if (GetConVarBool(g_AdminActivated)) {
				hQuery = INVALID_HANDLE;
				if(GetConVarBool(g_DroitUniqueAdmin)) Format(query, sizeof(query), "SELECT date_fin, port_serveur FROM `%s_droits` WHERE type_droit='2' AND user_id = '%i' AND port_serveur = '%i' AND date_fin > %i LIMIT 0,1", Prefix, Id_User, portserveur, timestamp);
				else Format(query, sizeof(query), "SELECT date_fin FROM `%s_droits` WHERE type_droit='2' AND user_id = '%i' AND date_fin > %i LIMIT 0,1", Prefix, Id_User, timestamp);
				hQuery = SQL_Query(db, query);
				
				if (SQL_FetchRow(hQuery))
					IsAdmin = true;
				else
					IsAdmin = false;
			}
						
			hQuery = INVALID_HANDLE;
			if(GetConVarBool(g_DroitUniqueVip)) Format(query, sizeof(query), "SELECT date_fin, port_serveur FROM `%s_droits` WHERE type_droit='1' AND user_id = '%i' AND port_serveur = '%i' AND date_fin > %i LIMIT 0,1", Prefix, Id_User, portserveur, timestamp);
			else Format(query, sizeof(query), "SELECT date_fin FROM `%s_droits` WHERE type_droit='1' AND user_id = '%i' AND date_fin > %i LIMIT 0,1", Prefix, Id_User, timestamp);
			
			hQuery = SQL_Query(db, query);

			if (SQL_FetchRow(hQuery)) IsVip = true;
			else IsVip = false;	
								
			if (IsVip && Suspended != 1) {
				if(GetUserFlagBits(client) != ADMFLAG_CUSTOM4) AddUserFlags(client, Admin_Custom4);
				if(GetUserFlagBits(client) != ADMFLAG_RESERVATION) AddUserFlags(client, Admin_Reservation);
				
				//CPrintToChatAll("{darkred}[DEBUG] {darkblue}: %N {darkred}vient d'obtenir son status VIP.", client);
			}
			else if (IsVip && Suspended) {
				AdminFlag adminFlags = Admin_Custom4|Admin_Reservation;
				RemoveUserFlags(client, adminFlags);
				
				//CPrintToChatAll("{darkred}[DEBUG] {darkblue}: %N {darkred} est suspendu.", client);
			}
						
			if (GetConVarBool(g_AdminActivated)) {
				if (IsAdmin && Suspended != 1) {
					clientFlags|= ADMFLAG_KICK|ADMFLAG_SLAY|ADMFLAG_GENERIC|ADMFLAG_CHAT|ADMFLAG_BAN;
					SetUserFlagBits(client, clientFlags);
					
					//CPrintToChatAll("{darkred}[DEBUG] {darkblue}: %N {darkred}vient d'obtenir son status ADMIN.", client);
				}
				else if (IsAdmin && Suspended) {
					AdminFlag adminFlags = Admin_Kick|Admin_Slay|Admin_Generic|Admin_Chat|Admin_Ban;
					RemoveUserFlags(client, adminFlags);  
					CPrintToChat(client,"{green}[Admin] {darkblue} Vos Droits d'Admin sont {darkred}suspendus{darkblue}, rendez-vous sur le site pour plus d'informations...");
				}
			}
		}
		if (hQuery != INVALID_HANDLE) {
			if (CloseHandle(hQuery)) {
				hQuery = INVALID_HANDLE;
			}
		}
	}
}