import { isURLScoped as g, setURLScope as b, getURLScope as R } from "@php-wasm/scopes";
import { portToStream as E } from "@php-wasm/universal";
const T = 25e3;
let x = 0;
function j(e, t, ...r) {
  const n = L();
  return e.postMessage(
    {
      ...t,
      requestId: n
    },
    ...r
  ), n;
}
function L() {
  return ++x;
}
function P(e, t, r = T) {
  return new Promise((n, a) => {
    const o = (u) => {
      u.data.type === "response" && u.data.requestId === t && (e.removeEventListener("message", o), clearTimeout(s), n(u.data.response));
    }, s = setTimeout(() => {
      a(new Error("Request timed out")), e.removeEventListener("message", o);
    }, r);
    e.addEventListener("message", o);
  });
}
function k(e, t) {
  return {
    type: "response",
    requestId: e,
    response: t
  };
}
async function v(e) {
  let t = new URL(e.request.url);
  if (!g(t))
    try {
      const c = new URL(e.request.referrer);
      t = b(t, R(c));
    } catch {
    }
  const r = e.request.headers.get("content-type"), n = e.request.method === "POST" ? new Uint8Array(await e.request.clone().arrayBuffer()) : void 0, a = {};
  for (const c of e.request.headers.entries())
    a[c[0]] = c[1];
  let o;
  try {
    const c = {
      method: "request",
      args: [
        {
          body: n,
          url: t.toString(),
          method: e.request.method,
          headers: {
            ...a,
            Host: t.host,
            // Safari and Firefox don't make the User-Agent header
            // available in the fetch event. Let's add it manually:
            "User-agent": self.navigator.userAgent,
            "Content-type": r
          }
        }
      ]
    }, i = R(t);
    if (i === null)
      throw new Error(
        `The URL ${t.toString()} is not scoped. This should not happen.`
      );
    const h = await A(c, i);
    if (o = await P(self, h), delete o.headers["x-frame-options"], o.headers["content-security-policy"]) {
      const l = o.headers["content-security-policy"].map(
        (d) => q(
          "frame-ancestors",
          d
        )
      ).filter((d) => d.trim().length > 0);
      l.length > 0 ? o.headers["content-security-policy"] = l : delete o.headers["content-security-policy"];
    }
  } catch (c) {
    throw console.error(c, { url: t.toString() }), c;
  }
  if (o.httpStatusCode >= 300 && o.httpStatusCode <= 399 && o.headers.location) {
    const c = R(t);
    let i = new URL(
      o.headers.location[0],
      t.toString()
    );
    return c && !g(i) && (i = b(i, c)), Response.redirect(
      i.toString(),
      o.httpStatusCode
    );
  }
  const s = [101, 103, 204, 205, 304].includes(
    o.httpStatusCode
  );
  let u = null;
  return s || (o.bodyPort ? u = E(o.bodyPort) : u = o.bytes), new Response(u, {
    headers: o.headers,
    status: o.httpStatusCode
  });
}
async function A(e, t) {
  const r = L();
  for (const n of await self.clients.matchAll({
    // Sometimes the client that triggered the current fetch()
    // event is considered uncontrolled in Google Chrome. This
    // only happens on the first few fetches() after the initial
    // registration of the service worker.
    includeUncontrolled: !0
  }))
    n.postMessage({
      ...e,
      /**
       * Attach the scope with a URL starting with `/scope:` to this message.
       *
       * We need this mechanics because this worker broadcasts
       * events to all the listeners across all browser tabs. Scopes
       * helps WASM workers ignore requests meant for other WASM workers.
       */
      scope: t,
      requestId: r
    });
  return r;
}
async function w(e, t) {
  let r;
  return ["GET", "HEAD"].includes(e.method) ? r = void 0 : "body" in t ? r = t.body : !e.bodyUsed && e.body ? r = e.body : r = await e.arrayBuffer(), new Request(t.url || e.url, {
    body: r,
    method: e.method,
    headers: e.headers,
    referrer: e.referrer,
    referrerPolicy: e.referrerPolicy,
    mode: e.mode === "navigate" ? "same-origin" : e.mode,
    credentials: e.credentials,
    cache: e.cache,
    redirect: e.redirect,
    integrity: e.integrity,
    /**
     * Infer the duplex value in a way that's consistent across browsers. Web browsers
     * only support 'half' as of January 2026, but other values may be supported in the future.
     * Unfortunately, also as of January 2026, we cannot read the duplex value directly from the
     * request object:
     *
     * > Although duplex can be passed as an option when constructing a Request,
     * > it is not currently exposed as a readable property on the resulting Request
     * > object in all browsers.
     *
     * See MDN: https://developer.mozilla.org/en-US/docs/Web/API/Request/duplex
     */
    ...r instanceof ReadableStream && { duplex: "half" },
    ...t
  });
}
async function H(e) {
  if (!e.body)
    return [e, e];
  const [t, r] = e.body.tee();
  return [
    await w(e, { body: t, duplex: "half" }),
    await w(e, { body: r, duplex: "half" })
  ];
}
function B(e) {
  const t = {};
  return e.headers.forEach((r, n) => {
    t[n] = r;
  }), t;
}
function q(e, t) {
  const r = /^[\u{9}\u{A}\u{C}\u{D}\u{20}]+/u, n = /[\u{9}\u{A}\u{C}\u{D}\u{20}]+$/u, a = /[\u{9}\u{A}\u{C}\u{D}\u{20}]/u;
  return t.split(";").filter((o) => {
    const s = o.replace(r, "").replace(n, ""), [u] = s.split(
      a,
      // The directive name is the first token.
      1
    );
    return u.toLowerCase() !== e.toLowerCase();
  }).join(";");
}
class I extends Error {
  constructor(t, r, n) {
    super(
      `Could not fetch ${t} – your network appears to be blocking this request (HTTP ${r}). This often happens on school, university, or corporate networks. Try switching to a different network or using a VPN.`
    ), this.name = "FirewallInterferenceError", this.url = t, this.status = r, this.statusText = n;
  }
}
const O = "X-Playground-Cors-Proxy";
async function M(e, t, r, n) {
  var h;
  let a = typeof e == "string" ? new Request(e, t) : e;
  const o = n ? new URL(n) : null;
  let s = o ? new URL(a.url, o) : new URL(a.url);
  if (s.hostname === "localhost" || s.hostname === "127.0.0.1" || s.hostname === "[::1]" || s.hostname === "::1")
    return await fetch(a);
  if (s.protocol === "http:") {
    s.protocol = "https:";
    const l = s.toString();
    a = await w(a, { url: l }), s = new URL(l);
  }
  if (!r)
    return await fetch(a);
  if (o && s.protocol === o.protocol && s.hostname === o.hostname && s.port === o.port && s.pathname.startsWith(o.pathname))
    return await fetch(a);
  const [c, i] = await H(a);
  try {
    return await fetch(c);
  } catch {
    const l = new Headers(i.headers), d = ((h = l.get("x-cors-proxy-allowed-request-headers")) == null ? void 0 : h.split(",")) || [], U = d.includes("authorization") || d.includes("cookie"), m = l.get("content-type");
    m && m.toLowerCase().includes("multipart/form-data") && (l.set("x-cors-proxy-content-type", m), l.set("content-type", "application/octet-stream"));
    const p = new URL(import.meta.url);
    p.pathname = "", p.search = "", p.hash = "";
    const S = new URL(r, p.toString());
    let f = i.body;
    f && new URL(S).protocol === "http:" && (f = await new Response(f).arrayBuffer());
    const C = await w(i, {
      url: `${r}${a.url}`,
      headers: l,
      body: f,
      ...U && { credentials: "include" }
    }), y = await fetch(C);
    if (!y.headers.has(O))
      throw new I(
        a.url,
        y.status,
        y.statusText
      );
    return y;
  }
}
export {
  I as FirewallInterferenceError,
  P as awaitReply,
  A as broadcastMessageExpectReply,
  w as cloneRequest,
  v as convertFetchEventToPHPRequest,
  M as fetchWithCorsProxy,
  L as getNextRequestId,
  B as getRequestHeaders,
  j as postMessageExpectReply,
  q as removeContentSecurityPolicyDirective,
  k as responseTo,
  H as teeRequest
};
