import React, { useState } from 'react';
import './Login.css';

export default function Login({ setToken }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    try {
      const res = await fetch('http://localhost:8000/api/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          // 'Accept': 'application/json', // Uncomment if needed
        },
        body: JSON.stringify({
          email: email,
          password: password
        })
      });
  
      // Check for network or CORS errors
      if (!res.ok) {
        // Try to parse error message from backend
        let errorMsg = 'Login failed';
        try {
          const errorData = await res.json();
          if (errorData.message) errorMsg = errorData.message;
        } catch {
          // Not JSON, keep default errorMsg
        }
        setError(errorMsg);
        setLoading(false);
        return;
      }
  
      const data = await res.json();
      if (data.access_token) {
        setToken(data.access_token);
        localStorage.setItem('token', data.access_token);
      } else {
        setError('Invalid email or password');
      }
    } catch (err) {
      setError('Network error or server not reachable');
    }
    setLoading(false);
  };

  return (
    <div className="login-bg">
      <form className="login-form" onSubmit={handleSubmit}>
        <h2>Netumo Login</h2>
        <div className="login-field">
          <label>Email</label>
          <input
            type="email"
            value={email}
            autoFocus
            onChange={e => setEmail(e.target.value)}
            placeholder="Enter your email"
            required
          />
        </div>
        <div className="login-field">
          <label>Password</label>
          <input
            type="password"
            value={password}
            onChange={e => setPassword(e.target.value)}
            placeholder="Enter your password"
            required
          />
        </div>
        <button type="submit" disabled={loading}>
          {loading ? 'Logging in...' : 'Login'}
        </button>
        {error && <div className="login-error">{error}</div>}
      </form>
    </div>
  );
}
