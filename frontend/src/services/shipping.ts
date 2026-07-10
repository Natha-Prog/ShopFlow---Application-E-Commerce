import api from '@/services/api'

export interface ShippingRegion {
  code: string
  name: string
  fee: number
}

export interface ShippingQuote {
  fee: number
  currency: string
  distance_km: number | null
  region: string | null
  message: string
}

export interface QuoteParams {
  shipping_method: 'pickup' | 'delivery' | 'shipping'
  latitude?: number
  longitude?: number
  region?: string
}

export async function getRegions(): Promise<ShippingRegion[]> {
  const { data } = await api.get<ShippingRegion[]>('/shipping/regions')
  return data
}

export async function quoteShipping(params: QuoteParams): Promise<ShippingQuote> {
  const { data } = await api.post<ShippingQuote>('/shipping/quote', params)
  return data
}
