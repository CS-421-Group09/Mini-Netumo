import React, { useEffect, useState } from 'react';
import { Box, AppBar, Toolbar, Typography, Drawer, List, ListItem, ListItemIcon, ListItemText, Badge } from '@mui/material';
import NotificationsIcon from '@mui/icons-material/Notifications';
import WebIcon from '@mui/icons-material/Web';
import AddCircleIcon from '@mui/icons-material/AddCircle';
import { Routes, Route, Link, useParams } from 'react-router-dom';
import Targets from './Targets';
import Alerts from './Alerts';
import CreateTarget from './CreateTarget';
import Status from './Status';
import History from './History';
import DashboardStats from './DashboardStats';
import api from '../api'; // Make sure this is the correct path

const drawerWidth = 220;

export default function Dashboard({ token, onLogout }) {
  const nodeId = process.env.REACT_APP_NODE_ID || 'unknown';
  const [targets, setTargets] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/targets')
      .then(res => setTargets(res.data))
      .finally(() => setLoading(false));
  }, []);

  const stats = {
    total: targets.length,
    up: targets.filter(t => t.latest_check?.is_success).length,
    down: targets.filter(t => t.latest_check && !t.latest_check.is_success).length,
    expiringSoon: targets.filter(
      t => (t.ssl_expiry_days != null && t.ssl_expiry_days <= 14) ||
           (t.domain_expiry_days != null && t.domain_expiry_days <= 14)
    ).length,
  };

  return (
    <Box sx={{ display: 'flex' }}>
      <AppBar position="fixed" sx={{ zIndex: 1201 }}>
        <Toolbar>
          <Typography variant="h6" noWrap component="div" sx={{ flexGrow: 1 }}>
            Netumo Dashboard
          </Typography>
          <Typography variant="body2" sx={{ mr: 2 }}>
            Node ID: {nodeId}
          </Typography>
          <button onClick={onLogout} style={{ color: 'white', background: 'transparent', border: '1px solid white', borderRadius: 4, padding: '6px 16px', cursor: 'pointer' }}>
            Logout
          </button>
        </Toolbar>
      </AppBar>
      <Drawer
        variant="permanent"
        sx={{
          width: drawerWidth,
          flexShrink: 0,
          [`& .MuiDrawer-paper`]: { width: drawerWidth, boxSizing: 'border-box' },
        }}
      >
        <Toolbar />
        <List>
          <ListItem button component={Link} to="/">
            <ListItemIcon><WebIcon /></ListItemIcon>
            <ListItemText primary="Targets" />
          </ListItem>
          <ListItem button component={Link} to="/alerts">
            <ListItemIcon>
              <Badge color="error" variant="dot">
                <NotificationsIcon />
              </Badge>
            </ListItemIcon>
            <ListItemText primary="Alerts" />
          </ListItem>
          <ListItem button component={Link} to="/create">
            <ListItemIcon><AddCircleIcon /></ListItemIcon>
            <ListItemText primary="Add Target" />
          </ListItem>
        </List>
      </Drawer>
      <Box component="main" sx={{ flexGrow: 1, bgcolor: 'background.default', p: 3 }}>
        <Toolbar />
        <DashboardStats stats={stats} />
        <Routes>
          <Route path="/" element={<Targets targets={targets} loading={loading} />} />
          <Route path="/alerts" element={<Alerts />} />
          <Route path="/create" element={<CreateTarget />} />
          <Route path="/target/:id" element={<TargetDetail />} />
        </Routes>
      </Box>
    </Box>
  );
}

// Helper component for target details

function TargetDetail() {
  const { id } = useParams();
  return (
    <div>
      <Status targetId={id} />
      <History targetId={id} />
    </div>
  );
}
