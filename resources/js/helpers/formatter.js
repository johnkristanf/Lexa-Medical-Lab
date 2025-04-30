
// DATE FORMATTER: 04/29/25 10:30AM
export const formatDate = (dateString) => {
    const date = new Date(dateString)

    const padZero = (num) => (num < 10 ? '0' + num : num)

    const month = padZero(date.getMonth() + 1) // Month is 0-based
    const day = padZero(date.getDate())
    const year = String(date.getFullYear()).slice(-2) // Get last 2 digits

    let hours = date.getHours()
    const minutes = padZero(date.getMinutes())
    const ampm = hours >= 12 ? 'PM' : 'AM'

    hours = hours % 12
    hours = hours ? hours : 12 // the hour '0' should be '12'

    return `${month}/${day}/${year} ${padZero(hours)}:${minutes}${ampm}`
}

// QUEUE STATUS FORMATTER
export const getStatusClasses = (status) => {
    switch (status) {
        case 'waiting':
            return 'bg-yellow-100 text-yellow-800'
        case 'serving':
            return 'bg-blue-100 text-blue-800'
        case 'served':
            return 'bg-green-100 text-green-800'
        case 'no_show':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}
