export function generateBatchNumber() {
    const now = new Date()
    const year = now.getFullYear().toString().slice(-2)
    const month = (now.getMonth() + 1).toString().padStart(2, '0')
    const day = now.getDate().toString().padStart(2, '0')
    const random = Math.floor(1000 + Math.random() * 9000) // 4-digit random

    return `BN-${day}${month}${year}-${random}`
}
