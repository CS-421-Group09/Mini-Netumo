import React, { useEffect, useState } from 'react';
import api from '../api';
import { LineChart, Line, XAxis, YAxis, Tooltip, CartesianGrid } from 'recharts';

export default function History({ targetId }) {
  const [history, setHistory] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get(`/history/${targetId}`)
      .then(res => setHistory(res.data.history))
      .catch(err => setError('Failed to load history'))
      .finally(() => setLoading(false));
  }, [targetId]);

  if (loading) return <div>Loading...</div>;
  if (error) return <div style={{color: 'red'}}>{error}</div>;
  if (!history.length) return <div>No history found.</div>;

  return (
    <div>
      <h3>History</h3>
      <LineChart width={400} height={200} data={history}>
        <XAxis dataKey="created_at" />
        <YAxis dataKey="latency" />
        <Tooltip />
        <CartesianGrid stroke="#eee" strokeDasharray="5 5" />
        <Line type="monotone" dataKey="latency" stroke="#8884d8" />
      </LineChart>
    </div>
  );
}
