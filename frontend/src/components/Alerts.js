import React, { useEffect, useState } from 'react';
import { Card, CardContent, Typography, Chip, Stack, Box, Select, MenuItem } from '@mui/material';
import WarningIcon from '@mui/icons-material/Warning';
import SecurityIcon from '@mui/icons-material/Security';
import LanguageIcon from '@mui/icons-material/Language';
import api from '../api';

const alertTypeInfo = {
  downtime: { label: 'Downtime', color: 'error', icon: <WarningIcon /> },
  ssl: { label: 'SSL Expiry', color: 'warning', icon: <SecurityIcon /> },
  domain: { label: 'Domain Expiry', color: 'info', icon: <LanguageIcon /> },
};

function groupLatestAlerts(alerts) {
  // Group by target_id + type, keep only the latest
  const map = {};
  alerts.forEach(alert => {
    const key = `${alert.target_id}-${alert.type}`;
    if (!map[key] || new Date(alert.created_at) > new Date(map[key].created_at)) {
      map[key] = alert;
    }
  });
  return Object.values(map);
}

export default function Alerts() {
  const [alerts, setAlerts] = useState([]);
  const [filter, setFilter] = useState('all');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/alerts')
      .then(res => setAlerts(res.data))
      .catch(() => setAlerts([]))
      .finally(() => setLoading(false));
  }, []);

  const groupedAlerts = groupLatestAlerts(alerts)
    .filter(a => filter === 'all' || a.type === filter);

  return (
    <Box>
      <Typography variant="h5" gutterBottom>Alerts</Typography>
      <Stack direction="row" spacing={2} mb={2}>
        <Select value={filter} onChange={e => setFilter(e.target.value)} size="small">
          <MenuItem value="all">All Types</MenuItem>
          <MenuItem value="downtime">Downtime</MenuItem>
          <MenuItem value="ssl">SSL Expiry</MenuItem>
          <MenuItem value="domain">Domain Expiry</MenuItem>
        </Select>
      </Stack>
      {loading ? <div>Loading...</div> : (
        <Stack spacing={2}>
          {groupedAlerts.length === 0 && <Typography>No alerts found.</Typography>}
          {groupedAlerts.map(alert => {
            const info = alertTypeInfo[alert.type] || {};
            return (
              <Card key={alert.id} sx={{ borderLeft: 4, borderColor: `${info.color}.main` }}>
                <CardContent>
                  <Stack direction="row" alignItems="center" spacing={2}>
                    {info.icon}
                    <Typography variant="subtitle1" color={info.color + ".main"}>
                      {info.label || alert.type}
                    </Typography>
                    <Chip label={new Date(alert.created_at).toLocaleString()} size="small" />
                  </Stack>
                  <Typography variant="body2" mt={1}>{alert.message}</Typography>
                </CardContent>
              </Card>
            );
          })}
        </Stack>
      )}
    </Box>
  );
}
