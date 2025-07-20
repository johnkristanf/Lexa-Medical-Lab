export function generateRandomNumberString(length) {
    if (length <= 0) return '';

    let result = '';
    for (let i = 0; i < length; i++) {
        const digit = Math.floor(Math.random() * 10);
        result += digit.toString();
    }
    return result;
}
