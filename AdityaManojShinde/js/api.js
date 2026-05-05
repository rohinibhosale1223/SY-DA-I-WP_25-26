const API_BASE_URL = window.location.pathname.includes('/pages/') ? '../backend/api.php' : './backend/api.php';

async function apiRequest(action, method = 'GET', data = null) {
    let url = `${API_BASE_URL}?action=${action}`;
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
        }
    };

    if (data) {
        if (method === 'GET' || method === 'DELETE') {
            const params = new URLSearchParams(data);
            url += `&${params.toString()}`;
        } else {
            options.body = JSON.stringify(data);
        }
    }

    try {
        const response = await fetch(url, options);
        
        // Handle potential non-JSON responses (e.g. PHP errors)
        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            console.error("Non-JSON response:", text);
            throw new Error("Server error occurred");
        }
        
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'API Request Failed');
        }
        
        return result;
    } catch (error) {
        console.error(`API Error (${action}):`, error);
        throw error;
    }
}
