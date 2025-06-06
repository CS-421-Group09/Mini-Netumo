import React, { useState } from 'react';
import api from '../api';
import { toast } from 'react-toastify';

export default function CreateTarget({ onCreated }) {
  const [name, setName] = useState('');
  const [url, setUrl] = useState('');
  const [checkFrequency, setCheckFrequency] = useState(5);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setSuccess('');
    try {
      await api.post('/targets', { name, url, check_frequency: checkFrequency });
      setSuccess('Target created!');
      toast.success('Target created!');
      setName('');
      setUrl('');
      setCheckFrequency(5);
      if (onCreated) onCreated();
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to create target');
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input value={name} onChange={e => setName(e.target.value)} placeholder="Name" required />
      <input value={url} onChange={e => setUrl(e.target.value)} placeholder="URL" required />
      <input type="number" value={checkFrequency} onChange={e => setCheckFrequency(e.target.value)} min={1} required />
      <button type="submit">Add Target</button>
      {success && <div style={{color: 'green'}}>{success}</div>}
      {error && <div style={{color: 'red'}}>{error}</div>}
    </form>
  );
}
