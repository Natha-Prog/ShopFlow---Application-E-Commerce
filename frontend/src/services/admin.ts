import api from './api'

export const adminApi = {
  dashboard: (days = 30) => api.get('/admin/dashboard/stats', { params: { days } }),
  products: {
    list: (params?: Record<string, string>) => api.get('/admin/products', { params }),
    create: (data: Record<string, unknown>) => api.post('/admin/products', data),
    update: (id: number, data: Record<string, unknown>) => api.put(`/admin/products/${id}`, data),
    delete: (id: number) => api.delete(`/admin/products/${id}`),
    uploadImage: (file: File) => {
      const form = new FormData()
      form.append('image', file)
      return api.post('/admin/products/upload-image', form, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
  categories: {
    list: () => api.get('/admin/categories'),
    create: (data: Record<string, unknown>) => api.post('/admin/categories', data),
    update: (id: number, data: Record<string, unknown>) => api.put(`/admin/categories/${id}`, data),
    delete: (id: number) => api.delete(`/admin/categories/${id}`),
  },
  orders: {
    list: (params?: Record<string, string>) => api.get('/admin/orders', { params }),
    update: (id: number, data: Record<string, unknown>) => api.put(`/admin/orders/${id}`, data),
    export: () => api.get('/admin/orders/export', { responseType: 'blob' }),
  },
  promotions: {
    list: () => api.get('/admin/promotions'),
    create: (data: Record<string, unknown>) => api.post('/admin/promotions', data),
    update: (id: number, data: Record<string, unknown>) => api.put(`/admin/promotions/${id}`, data),
    delete: (id: number) => api.delete(`/admin/promotions/${id}`),
  },
  users: {
    list: (params?: Record<string, string>) => api.get('/admin/users', { params }),
    create: (data: Record<string, unknown>) => api.post('/admin/users', data),
    update: (id: number, data: Record<string, unknown>) => api.put(`/admin/users/${id}`, data),
    delete: (id: number) => api.delete(`/admin/users/${id}`),
  },
}
