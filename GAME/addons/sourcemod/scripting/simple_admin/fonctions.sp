public void ConnectDb() {
	char error[255];
	db = SQL_Connect("autovip", true, error, sizeof(error));
	if (db == INVALID_HANDLE)
		connexion = false;
	else
	{
		connexion = true;
		
		for(int i=1; i <= GetMaxClients(); i++)
			if (IsClientInGame(i) && !IsFakeClient(i))
				Droit(i);
	}
}

public void DisconnectDb() {
	if (db != INVALID_HANDLE) {
		connexion = false;
		CloseHandle(db);
		db = INVALID_HANDLE;
	}
}