import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { LineChart, Line, XAxis, YAxis, Tooltip, Legend, CartesianGrid } from 'recharts';
import {
  Table, TableHead, TableRow, TableCell, TableBody, Paper, Chip, Tooltip as MuiTooltip, Typography, Box, TextField, MenuItem
} from '@mui/material';
import LinearProgress from '@mui/material/LinearProgress';

export default function Targets({ targets, loading }) {
  const [error, setError] = useState('');
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');


  if (loading) return <div>Loading...</div>;

  // Helper to calculate days left
  const daysLeft = (days) => days != null ? days : null;

  const filteredTargets = targets.filter(t => {
    const matchesSearch = t.name.toLowerCase().includes(search.toLowerCase());
    const matchesStatus =
      !statusFilter ||
      (statusFilter === 'up' && t.latest_check?.is_success) ||
      (statusFilter === 'down' && t.latest_check && !t.latest_check.is_success);
    return matchesSearch && matchesStatus;
  });

  return (
    <Box sx={{ maxWidth: 1100, margin: 'auto', mt: 4 }}>
      <Typography variant="h4" gutterBottom>Targets</Typography>
      {error && <div style={{color: 'red'}}>{error}</div>}
      <Box sx={{ display: 'flex', gap: 2, mb: 2 }}>
        <TextField
          label="Search"
          value={search}
          onChange={e => setSearch(e.target.value)}
          size="small"
        />
        <TextField
          select
          label="Status"
          value={statusFilter}
          onChange={e => setStatusFilter(e.target.value)}
          size="small"
          sx={{ minWidth: 120 }}
        >
          <MenuItem value="">All</MenuItem>
          <MenuItem value="up">Up</MenuItem>
          <MenuItem value="down">Down</MenuItem>
        </TextField>
      </Box>
      <Paper elevation={3} sx={{ overflowX: 'auto' }}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>Name</TableCell>
              <TableCell>Status</TableCell>
              <TableCell>Latency</TableCell>
              <TableCell>SSL Expiry</TableCell>
              <TableCell>Domain Expiry</TableCell>
              <TableCell>Uptime (24h)</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {filteredTargets.map(t => {
              const sslDays = daysLeft(t.ssl_expiry_days);
              const domainDays = daysLeft(t.domain_expiry_days);
              return (
                <TableRow key={t.id} hover>
                  <TableCell>
                    <Link to={`/target/${t.id}`} style={{ textDecoration: 'none', color: '#1976d2', fontWeight: 500 }}>{t.name}</Link>
                  </TableCell>
                  <TableCell>
                    <Chip
                      label={t.latest_check?.is_success ? 'Up' : 'Down'}
                      color={t.latest_check?.is_success ? 'success' : 'error'}
                      size="small"
                      sx={{ fontWeight: 'bold' }}
                    />
                  </TableCell>
                  <TableCell>
                    {t.latest_check?.latency != null ? `${t.latest_check.latency} ms` : 'N/A'}
                  </TableCell>
                  <TableCell>
                    {sslDays != null ? (
                      <Box sx={{ minWidth: 80 }}>
                        <Chip
                          label={`${sslDays} days`}
                          color={sslDays <= 7 ? 'error' : sslDays <= 14 ? 'warning' : 'default'}
                          size="small"
                          sx={{ fontWeight: sslDays <= 7 ? 'bold' : 'normal', mb: 0.5 }}
                        />
                        <LinearProgress
                          variant="determinate"
                          value={Math.max(0, Math.min(100, (sslDays / 90) * 100))}
                          color={sslDays <= 7 ? 'error' : sslDays <= 14 ? 'warning' : 'primary'}
                          sx={{ height: 6, borderRadius: 3 }}
                        />
                      </Box>
                    ) : 'N/A'}
                  </TableCell>
                  <TableCell>
                    {domainDays != null ? (
                      <Box sx={{ minWidth: 80 }}>
                        <Chip
                          label={`${domainDays} days`}
                          color={domainDays <= 7 ? 'error' : domainDays <= 14 ? 'warning' : 'default'}
                          size="small"
                          sx={{ fontWeight: domainDays <= 7 ? 'bold' : 'normal', mb: 0.5 }}
                        />
                        <LinearProgress
                          variant="determinate"
                          value={Math.max(0, Math.min(100, (domainDays / 365) * 100))}
                          color={domainDays <= 7 ? 'error' : domainDays <= 14 ? 'warning' : 'primary'}
                          sx={{ height: 6, borderRadius: 3 }}
                        />
                      </Box>
                    ) : 'N/A'}
                  </TableCell>
                  <TableCell>
                    {t.last_24h_checks && t.last_24h_checks.length > 0 ? (
                      <LineChart width={140} height={40} data={t.last_24h_checks} margin={{ left: 0, right: 0, top: 5, bottom: 5 }}>
                        <XAxis dataKey="created_at" hide />
                        <YAxis yAxisId="uptime" domain={[-0.1, 1.1]} hide />
                        <YAxis yAxisId="latency" orientation="right" hide />
                        <Tooltip
                          formatter={(value, name) =>
                            name === 'is_success'
                              ? value ? 'Up' : 'Down'
                              : `${value} ms`
                          }
                          labelFormatter={label => `Time: ${label}`}
                        />
                        <Line
                          type="monotone"
                          dataKey="is_success"
                          yAxisId="uptime"
                          stroke="#4caf50"
                          dot={{ r: 3, fill: '#4caf50' }}
                          activeDot={{ r: 5 }}
                          strokeWidth={0}
                          name="Uptime"
                        />
                        <Line
                          type="monotone"
                          dataKey="latency"
                          yAxisId="latency"
                          stroke="#8884d8"
                          dot={false}
                          strokeWidth={2}
                          name="Latency (ms)"
                        />
                      </LineChart>
                    ) : 'N/A'}
                  </TableCell>
                </TableRow>
              );
            })}
          </TableBody>
        </Table>
      </Paper>
    </Box>
  );
}