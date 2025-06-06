import React from 'react';
import { useParams, Link } from 'react-router-dom';
import Status from '../components/Status';
import History from '../components/History';
import { Button, Box, Tabs, Tab } from '@mui/material';

export default function TargetDetail() {
  const { id } = useParams();
  const [tab, setTab] = React.useState(0);

  return (
    <Box sx={{ maxWidth: 900, margin: 'auto', mt: 4 }}>
      <Button component={Link} to="/" variant="outlined" sx={{ mb: 2 }}>
        ← Back to Dashboard
      </Button>
      <Tabs value={tab} onChange={(_, v) => setTab(v)} sx={{ mb: 2 }}>
        <Tab label="Status" />
        <Tab label="History" />
        {/* <Tab label="Alerts" /> */}
      </Tabs>
      {tab === 0 && <Status targetId={id} />}
      {tab === 1 && <History targetId={id} />}
      {/* {tab === 2 && <Alerts targetId={id} />} */}
    </Box>
  );
}
