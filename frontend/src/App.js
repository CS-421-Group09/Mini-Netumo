import React, { useState } from 'react';
import { BrowserRouter as Router, Route, Routes, Navigate } from 'react-router-dom';
import Login from './components/Login';
import Dashboard from './components/Dashboard';
import './App.css';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { NotifierProvider } from './components/Notifier';

function App() {
  // Always clear token on app load to force login every time
  React.useEffect(() => {
    localStorage.removeItem('token');
  }, []);

  const [token, setToken] = useState(null);

  // Optionally, add a logout function
  const handleLogout = () => {
    setToken(null);
    localStorage.removeItem('token');
  };

  if (!token) {
    return <Login setToken={setToken} />;
  }

  return (
    <Router>
      <NotifierProvider>
        <div>
          <Routes>
            <Route path="/*" element={<Dashboard token={token} onLogout={handleLogout} />} />
          </Routes>
          <ToastContainer />
        </div>
      </NotifierProvider>
    </Router>
  );
}

export default App;
