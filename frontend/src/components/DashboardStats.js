import React from 'react';
import { Card, CardContent, Typography, Grid } from '@mui/material';
import { ArrowUpward, ArrowDownward, Warning } from '@mui/icons-material';

export default function DashboardStats({ stats }) {
  return (
    <Grid container spacing={2} sx={{ mb: 3 }}>
      <Grid item xs={12} sm={4}>
        <Card>
          <CardContent>
            <Typography variant="h6">Total Targets</Typography>
            <Typography variant="h4">{stats.total}</Typography>
          </CardContent>
        </Card>
      </Grid>
      <Grid item xs={6} sm={4}>
        <Card sx={{ bgcolor: '#e8f5e9' }}>
          <CardContent>
            <Typography variant="h6" color="success.main">
              Up <ArrowUpward color="success" />
            </Typography>
            <Typography variant="h4" color="success.main">{stats.up}</Typography>
          </CardContent>
        </Card>
      </Grid>
      <Grid item xs={6} sm={4}>
        <Card sx={{ bgcolor: '#ffebee' }}>
          <CardContent>
            <Typography variant="h6" color="error.main">
              Down <ArrowDownward color="error" />
            </Typography>
            <Typography variant="h4" color="error.main">{stats.down}</Typography>
          </CardContent>
        </Card>
      </Grid>
      <Grid item xs={12} sm={4}>
        <Card sx={{ bgcolor: '#fffde7' }}>
          <CardContent>
            <Typography variant="h6" color="warning.main">
              Expiring Soon <Warning color="warning" />
            </Typography>
            <Typography variant="h4" color="warning.main">{stats.expiringSoon}</Typography>
          </CardContent>
        </Card>
      </Grid>
    </Grid>
  );
}
