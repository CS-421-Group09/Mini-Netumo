import axios from 'axios';

const api = axios.create({
  baseURL: 'http://16.171.253.227/api',
});

// Always use the latest token from localStorage
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
