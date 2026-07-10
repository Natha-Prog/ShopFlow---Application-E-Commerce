import { ref, computed } from 'vue'

const currencies = {
  MGA: { symbol: 'MGA', rate: 1, name: 'Malagasy Ariary' }
}

const currentCurrency = ref('MGA')

export function useCurrency() {
  const currencyInfo = computed(() => currencies[currentCurrency.value as keyof typeof currencies])
  
  function formatPrice(price: number): string {
    const convertedPrice = price * currencyInfo.value.rate
    const formattedPrice = Math.round(convertedPrice).toLocaleString('fr-FR')
    return `${formattedPrice} ${currencyInfo.value.symbol}`
  }
  
  function setCurrency(currency: keyof typeof currencies) {
    currentCurrency.value = currency
    localStorage.setItem('currency', currency)
  }
  
  function getCurrency() {
    return currentCurrency.value
  }
  
  function convertPrice(price: number): number {
    return price * currencyInfo.value.rate
  }
  
  return {
    currencyInfo,
    formatPrice,
    setCurrency,
    getCurrency,
    convertPrice
  }
}
