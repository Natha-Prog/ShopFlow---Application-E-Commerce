import api from '@/services/api'

export interface PaymentMethod {
  id: string
  name: string
  description: string
  requires_phone: boolean
  merchant_number: string | null
  merchant_name: string | null
  bank_name: string | null
  account_number: string | null
  account_holder: string | null
  instructions: string | null
  prefixes: string[]
}

export interface PaymentMethodsResponse {
  currency: string
  methods: PaymentMethod[]
}

export interface Transaction {
  id: number
  reference: string
  status: 'pending' | 'processing' | 'completed' | 'failed' | 'cancelled'
  amount: number
  provider: string
  ussd_code: string
  qr_code_data: string
  expires_at: string
  completed_at: string | null
}

export interface InitiatePaymentRequest {
  order_id: number
  provider: 'mvola' | 'orange_money' | 'airtel_money'
  customer_phone: string
}

export async function getPaymentMethods(): Promise<PaymentMethodsResponse> {
  const { data } = await api.get<PaymentMethodsResponse>('/payments/methods')
  return data
}

export async function initiatePayment(request: InitiatePaymentRequest): Promise<Transaction> {
  const { data } = await api.post<Transaction>('/payments/initiate', request)
  return data
}

export async function getTransactionStatus(transactionId: number): Promise<Transaction> {
  const { data } = await api.get<Transaction>(`/payments/${transactionId}/status`)
  return data
}

export async function getUSSDCode(transactionId: number): Promise<{ ussd_code: string; expires_at: string }> {
  const { data } = await api.get<{ ussd_code: string; expires_at: string }>(`/payments/${transactionId}/ussd`)
  return data
}

export async function getQRCode(transactionId: number): Promise<{ qr_code_data: string; expires_at: string }> {
  const { data } = await api.get<{ qr_code_data: string; expires_at: string }>(`/payments/${transactionId}/qrcode`)
  return data
}
