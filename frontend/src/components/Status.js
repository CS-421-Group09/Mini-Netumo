import React, { useEffect, useState } from 'react';
import api from '../api';

export default function Status({ targetId }) {
  const [status, setStatus] = useState(null);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get(`/status/${targetId}`)
      .then(res => setStatus(res.data))
      .catch(err => setError('Failed to load status'))
      .finally(() => setLoading(false));
  }, [targetId]);

  if (loading) return <div>Loading...</div>;
  if (error) return <div style={{color: 'red'}}>{error}</div>;
  if (!status) return <div>No status found.</div>;

  return (
    <div>
      <h3>Status for {status.target.name}</h3>
      <div>
        Status: <span style={{
          color: status.latest_check?.is_success ? 'green' : 'red',
          fontWeight: 'bold'
        }}>
          {status.latest_check?.is_success ? 'Up' : 'Down'}
        </span>
      </div>
      <div>Latency: {status.latest_check?.latency} ms</div>
    </div>
  );
}
