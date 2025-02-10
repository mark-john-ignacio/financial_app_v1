export class POSUtils {
    static formatDate(date) {
        const d = new Date(date);
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const year = d.getFullYear();
        
        return `${month}/${day}/${year}`;
    }

    static formatCurrency(amount) {
        return parseFloat(amount).toFixed(2);
    }

    static formatTime(date) {
        const d = new Date(date);
        const hours = String(d.getHours() % 12 || 12).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        const seconds = String(d.getSeconds()).padStart(2, '0');
        
        return `${hours}:${minutes}:${seconds}`;
    }

    static checkAccess(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "Function/th_useraccess.php",
                data: { id },
                dataType: 'json',
                success: res => resolve(res.valid),
                error: err => reject(err)
            });
        });
    }

    static parseAmount(value) {
        return parseFloat(value.replace(/,/g, '') || 0);
    }

    static delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}