import React, { createContext, useContext, useState } from 'react';
import { Snackbar, Alert } from '@mui/material';

const NotifierContext = createContext();

export function useNotifier() {
  return useContext(NotifierContext);
}

export function NotifierProvider({ children }) {
  const [msg, setMsg] = useState(null);

  const notify = (message, severity = 'info') => setMsg({ message, severity });

  return (
    <NotifierContext.Provider value={notify}>
      {children}
      <Snackbar
        open={!!msg}
        autoHideDuration={4000}
        onClose={() => setMsg(null)}
        anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
      >
        {msg && <Alert severity={msg.severity}>{msg.message}</Alert>}
      </Snackbar>
    </NotifierContext.Provider>
  );
}
