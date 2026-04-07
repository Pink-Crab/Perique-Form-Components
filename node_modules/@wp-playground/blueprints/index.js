import "@php-wasm/node-polyfills";
import { cloneStreamMonitorProgress as Zr, cloneResponseMonitorProgress as Qr, ProgressTracker as Yr } from "@php-wasm/progress";
import { randomFilename as ir, phpVars as mr, joinPaths as ie, phpVar as be, dirname as yr, Semaphore as qr, normalizePath as Gr, basename as kr } from "@php-wasm/util";
import { writeFiles as hr, LatestSupportedPHPVersion as Jr, SupportedPHPVersions as Xr } from "@php-wasm/universal";
import { resolveCommitHash as Kr, listGitFiles as et, listDescendantFiles as rt, sparseCheckout as tt, createDotGitDirectory as st, GitAuthenticationError as Or, OverlayFilesystem as it, InMemoryFilesystem as ot, FetchFilesystem as at, ZipFilesystem as nt, ChrootFilesystem as pt } from "@wp-playground/storage";
import { fetchWithCorsProxy as lt } from "@php-wasm/web-service-worker";
import { encodeZip as ft, collectFile as ut, StreamedFile as dt } from "@php-wasm/stream-compression";
import { logger as ae } from "@php-wasm/logger";
import { defineWpConfigConstants as ct, ensureWpConfig as mt } from "@wp-playground/wordpress";
import { unzipFile as Ar, RecommendedPHPVersion as yt } from "@wp-playground/common";
function Rr(r) {
  return r && "read" in r && typeof r.read == "function";
}
async function ht(r) {
  if (!Rr(r))
    return r;
  const n = await (await r.read("blueprint.json")).text();
  return JSON.parse(n);
}
class or {
  static async create(t) {
    const n = await ht(t), l = Rr(t) ? t : void 0;
    return or.createFromDeclaration(n, l);
  }
  static createFromDeclaration(t, n = void 0) {
    return new or(
      t,
      n,
      t.version || 1
    );
  }
  constructor(t, n, l) {
    this.declaration = t, this.bundle = n, this.version = l;
  }
  getVersion() {
    return this.version;
  }
  getDeclaration() {
    return this.declaration;
  }
  isBundle() {
    return this.bundle !== void 0;
  }
  getBundle() {
    return this.bundle;
  }
  getBlueprint() {
    return this.getBundle() || this.getDeclaration();
  }
}
function ar(r) {
  const t = r.split(".").shift().replace(/-/g, " ");
  return t.charAt(0).toUpperCase() + t.slice(1).toLowerCase();
}
const gt = `Blueprint resource of type "bundled" requires a filesystem.

This Blueprint refers to files that should be bundled with it (like images, plugins, or themes), but the filesystem needed to access these files is not available. This usually happens when:

1. You're trying to load a Blueprint as a standalone JSON file that was meant to be part of a bundle
2. The Blueprint was not packaged correctly as a blueprint.zip file

To fix this:
• If you're loading from a URL, make sure all referenced files are accessible relative to the Blueprint file
• If you're using a blueprint.zip file, ensure it contains all the files referenced in the Blueprint
• Check that the "resource": "bundled" references in your Blueprint match actual files in your bundle

Learn more about Blueprint resources: https://wordpress.github.io/wordpress-playground/blueprints/data-format#resources`;
class bt extends Error {
  constructor(t = gt) {
    super(t), this.name = "BlueprintFilesystemRequiredError";
  }
}
class fr extends Error {
  constructor(t, n, l) {
    super(t, l), this.name = "ResourceDownloadError", this.url = n;
  }
}
const wt = [
  "vfs",
  "literal",
  "wordpress.org/themes",
  "wordpress.org/plugins",
  "url",
  "git:directory",
  "bundled",
  "zip"
];
function vt(r) {
  return r && typeof r == "object" && typeof r.resource == "string" && wt.includes(r.resource);
}
function Pt(r) {
  try {
    return new URL(r).hostname === "github-proxy.com";
  } catch {
    return !1;
  }
}
function _t(r) {
  let t;
  try {
    t = new URL(r);
  } catch {
    return null;
  }
  if (t.hostname !== "github-proxy.com")
    return null;
  const n = t.pathname.slice(1);
  if (n.startsWith("https://github.com/") || n.startsWith("http://github.com/"))
    return { resource: "url", url: n };
  const l = t.searchParams, u = l.get("repo");
  if (!u)
    return null;
  const s = l.get("release"), e = l.get("asset");
  if (s && e) {
    const $ = s === "latest" ? "releases/latest/download" : `releases/download/${s}`;
    return {
      resource: "url",
      url: `https://github.com/${u}/${$}/${e}`
    };
  }
  let p, d;
  const y = l.get("pr"), _ = l.get("commit"), h = l.get("branch");
  y ? p = `refs/pull/${y}/head` : _ ? (p = _, d = "commit") : s ? (p = s, d = "tag") : p = h || "HEAD";
  const k = l.get("directory");
  return {
    resource: "zip",
    inner: {
      resource: "git:directory",
      url: `https://github.com/${u}`,
      ref: p,
      ...d && { refType: d },
      ...k && { path: k }
    }
  };
}
class we {
  get progress() {
    return this._progress;
  }
  set progress(t) {
    this._progress = t;
  }
  setPlayground(t) {
    this.playground = t;
  }
  /** Whether this Resource is loaded asynchronously */
  get isAsync() {
    return !1;
  }
  /**
   * Creates a new Resource based on the given file reference
   *
   * @param ref The file reference to create the Resource for
   * @param options Additional options for the Resource
   * @returns A new Resource instance
   */
  static create(t, {
    semaphore: n,
    progress: l,
    corsProxy: u,
    streamBundledFile: s,
    gitAdditionalHeadersCallback: e
  }) {
    if (t.resource === "url" && Pt(t.url)) {
      const d = _t(t.url);
      d && (console.warn(
        `[Blueprints] github-proxy.com is deprecated and will stop working soon. The URL "${t.url}" has been automatically converted to a ${d.resource} resource. Please update your Blueprint to use native resource types. See: https://wordpress.github.io/wordpress-playground/blueprints/steps/resources`
      ), t = d);
    }
    let p;
    switch (t.resource) {
      case "vfs":
        p = new kt(t, l);
        break;
      case "literal":
        p = new Ot(t, l);
        break;
      case "wordpress.org/themes":
        p = new Rt(t, l);
        break;
      case "wordpress.org/plugins":
        p = new St(t, l);
        break;
      case "url":
        p = new $t(t, l, { corsProxy: u });
        break;
      case "git:directory":
        p = new jt(t, l, {
          corsProxy: u,
          additionalHeaders: e
        });
        break;
      case "literal:directory":
        p = new At(t, l);
        break;
      case "bundled":
        if (!s)
          throw new bt();
        p = new Nt(
          t,
          s,
          l
        );
        break;
      case "zip": {
        const d = we.create(t.inner, {
          semaphore: n,
          progress: l,
          corsProxy: u,
          streamBundledFile: s,
          gitAdditionalHeadersCallback: e
        });
        p = new xt(t, d, l);
        break;
      }
      default:
        throw new Error(
          `Unknown resource type: ${t.resource}`
        );
    }
    return n && (p = new Ft(p, n)), new Lt(p);
  }
}
class Sr extends we {
  constructor(t) {
    super(), this.resource = t;
  }
  /** @inheritDoc */
  get progress() {
    return this.resource.progress;
  }
  /** @inheritDoc */
  set progress(t) {
    this.resource.progress = t;
  }
  /** @inheritDoc */
  get name() {
    return this.resource.name;
  }
  /** @inheritDoc */
  get isAsync() {
    return this.resource.isAsync;
  }
  /** @inheritDoc */
  setPlayground(t) {
    this.resource.setPlayground(t);
  }
}
class kt extends we {
  /**
   * Creates a new instance of `VFSResource`.
   * @param playground The playground client.
   * @param resource The VFS reference.
   * @param progress The progress tracker.
   */
  constructor(t, n) {
    super(), this.resource = t, this._progress = n;
  }
  /** @inheritDoc */
  async resolve() {
    var n;
    const t = await this.playground.readFileAsBuffer(
      this.resource.path
    );
    return (n = this.progress) == null || n.set(100), new File([t], this.name);
  }
  /** @inheritDoc */
  get name() {
    return this.resource.path.split("/").pop() || "";
  }
}
class Ot extends we {
  /**
   * Creates a new instance of `LiteralResource`.
   * @param resource The literal reference.
   * @param progress The progress tracker.
   */
  constructor(t, n) {
    super(), this.resource = t, this._progress = n;
  }
  /** @inheritDoc */
  async resolve() {
    var t;
    return (t = this.progress) == null || t.set(100), new File([this.resource.contents], this.resource.name);
  }
  /** @inheritDoc */
  get name() {
    return this.resource.name;
  }
}
class gr extends we {
  /**
   * Creates a new instance of `FetchResource`.
   * @param progress The progress tracker.
   */
  constructor(t, n) {
    super(), this._progress = t, this.corsProxy = n;
  }
  /** @inheritDoc */
  async resolve() {
    var n, l, u;
    (n = this.progress) == null || n.setCaption(this.caption);
    const t = this.getURL();
    try {
      let s = await lt(
        t,
        void 0,
        this.corsProxy,
        await ((l = this.playground) == null ? void 0 : l.absoluteUrl)
      );
      if (!s.ok)
        throw new fr(
          `Could not download "${t}"`,
          t
        );
      if (s = await Qr(
        s,
        ((u = this.progress) == null ? void 0 : u.loadingListener) ?? Et
      ), s.status !== 200)
        throw new fr(
          `Could not download "${t}"`,
          t
        );
      const e = this.name || Tt(
        s.headers.get("content-disposition") || ""
      ) || encodeURIComponent(t);
      return new File([await s.arrayBuffer()], e);
    } catch (s) {
      throw new fr(
        `Could not download "${t}".

Confirm that the URL is correct, the server is reachable, and the file is actually served at that URL. Original error: 
 ${s}`,
        t,
        { cause: s }
      );
    }
  }
  /**
   * Gets the caption for the progress tracker.
   * @returns The caption.
   */
  get caption() {
    return `Downloading ${this.name}`;
  }
  /** @inheritDoc */
  get name() {
    try {
      return new URL(this.getURL(), "http://example.com").pathname.split("/").pop();
    } catch {
      return this.getURL();
    }
  }
  /** @inheritDoc */
  get isAsync() {
    return !0;
  }
}
function Tt(r) {
  if (!r)
    return null;
  const t = r.match(/filename\*?=([^;]+)/i);
  if (!t)
    return null;
  let n = t[1].trim();
  if ((n.startsWith('"') && n.endsWith('"') || n.startsWith("'") && n.endsWith("'")) && (n = n.slice(1, -1)), t[0].includes("filename*")) {
    const l = n.match(/^[^']*'[^']*'(.+)$/);
    if (l)
      try {
        n = decodeURIComponent(l[1]);
      } catch {
      }
  }
  return n;
}
const Et = () => {
};
class $t extends gr {
  /**
   * Creates a new instance of `UrlResource`.
   * @param resource The URL reference.
   * @param progress The progress tracker.
   */
  constructor(t, n, l) {
    if (super(n, l == null ? void 0 : l.corsProxy), this.resource = t, this.options = l, this.resource.url.startsWith("https://github.com/")) {
      const u = this.resource.url.match(
        /^https:\/\/github\.com\/(?<owner>[^/]+)\/(?<repo>[^/]+)\/(?:blob|raw)\/(?<branch>[^/]+)\/(?<path>.+[^/])$/
      );
      u != null && u.groups && (this.resource = {
        ...this.resource,
        url: `https://raw.githubusercontent.com/${u.groups.owner}/${u.groups.repo}/${u.groups.branch}/${u.groups.path}`
      });
    }
  }
  /** @inheritDoc */
  getURL() {
    return this.resource.url;
  }
  /** @inheritDoc */
  get caption() {
    return this.resource.caption ?? super.caption;
  }
}
class jt extends we {
  constructor(t, n, l) {
    super(), this.reference = t, this._progress = n, this.options = l;
  }
  async resolve() {
    var l, u, s;
    const t = ((u = (l = this.options) == null ? void 0 : l.additionalHeaders) == null ? void 0 : u.call(l, this.reference.url)) ?? {}, n = (s = this.options) != null && s.corsProxy ? `${this.options.corsProxy}${this.reference.url}` : this.reference.url;
    try {
      const e = await Kr(
        n,
        {
          value: this.reference.ref,
          type: this.reference.refType ?? "infer"
        },
        t
      ), p = await et(
        n,
        e,
        t
      ), d = (this.reference.path ?? "").replace(
        /^\/+/,
        ""
      ), y = rt(p, d), _ = await tt(
        n,
        e,
        y,
        {
          withObjects: this.reference[".git"],
          additionalHeaders: t
        }
      );
      let h = _.files;
      return h = qt(
        h,
        (k) => k.substring(d.length).replace(/^\/+/, "")
      ), this.reference[".git"] && (h = {
        ...await st({
          repoUrl: this.reference.url,
          commitHash: e,
          ref: this.reference.ref,
          refType: this.reference.refType,
          objects: _.objects ?? [],
          fileOids: _.fileOids ?? {},
          pathPrefix: d
        }),
        ...h
      }), {
        name: this.filename,
        files: h
      };
    } catch (e) {
      throw e instanceof Or ? new Or(
        this.reference.url,
        e.status
      ) : e;
    }
  }
  /**
   * Generate a nice, non-empty filename – the installPlugin step depends on it.
   */
  get filename() {
    return this.name.replaceAll(/[^a-zA-Z0-9-.]/g, "-").replaceAll(/-+/g, "-").replace(/^[^a-zA-Z0-9]+|[^a-zA-Z0-9]+$/g, "") || ir();
  }
  /** @inheritDoc */
  get name() {
    var t;
    return [
      this.reference.url,
      this.reference.ref ? `(${this.reference.ref})` : "",
      (t = this.reference.path) != null && t.replace(/^\/+/, "") ? `at ${this.reference.path}` : ""
    ].filter((n) => n.length > 0).join(" ");
  }
}
function qt(r, t) {
  return Object.fromEntries(
    Object.entries(r).map(([n, l]) => [t(n), l])
  );
}
class At extends we {
  constructor(t, n) {
    super(), this.reference = t, this._progress = n;
  }
  async resolve() {
    return this.reference;
  }
  /** @inheritDoc */
  get name() {
    return this.reference.name;
  }
}
class Rt extends gr {
  constructor(t, n) {
    super(n), this.resource = t;
  }
  get name() {
    return ar(this.resource.slug);
  }
  getURL() {
    return `https://downloads.wordpress.org/theme/${Lr(this.resource.slug)}`;
  }
}
class St extends gr {
  constructor(t, n) {
    super(n), this.resource = t;
  }
  /** @inheritDoc */
  get name() {
    return ar(this.resource.slug);
  }
  /** @inheritDoc */
  getURL() {
    return `https://downloads.wordpress.org/plugin/${Lr(this.resource.slug)}`;
  }
}
function Lr(r) {
  return !r || r.endsWith(".zip") ? r : r + ".latest-stable.zip";
}
class Lt extends Sr {
  /** @inheritDoc */
  async resolve() {
    return this.promise || (this.promise = this.resource.resolve()), this.promise;
  }
}
class Ft extends Sr {
  constructor(t, n) {
    super(t), this.semaphore = n;
  }
  /** @inheritDoc */
  async resolve() {
    return this.isAsync ? this.semaphore.run(() => this.resource.resolve()) : this.resource.resolve();
  }
}
class Nt extends we {
  /**
   * Creates a new instance of `BlueprintResource`.
   * @param resource The blueprint reference.
   * @param filesystem The filesystem to read from.
   * @param progress The progress tracker.
   */
  constructor(t, n, l) {
    if (!n)
      throw new Error(
        `You are trying to run a Blueprint that refers to a bundled file ("blueprint" resource type), but you did not provide the rest of the bundle. This Blueprint won't work as a standalone JSON file. You'll need to load the entire bundle, e.g. a blueprint.zip file. Alternatively, you may try loading it directly from a URL or a local directory and Playground will try (with your permission) to source the missing files from paths relative to the blueprint file.`
      );
    super(), this.resource = t, this.streamBundledFile = n, this._progress = l;
  }
  /** @inheritDoc */
  async resolve() {
    var t, n, l;
    (t = this.progress) == null || t.set(0);
    try {
      const u = await this.streamBundledFile(this.resource.path), s = u.filesize;
      if (!s)
        return (n = this.progress) == null || n.set(100), u;
      const e = Zr(
        u.stream(),
        s,
        (p) => {
          var d;
          (d = this.progress) == null || d.set(
            p.detail.loaded / p.detail.total * 100
          );
        }
      );
      return new dt(e, this.name, {
        filesize: s
      });
    } catch (u) {
      throw (l = this.progress) == null || l.set(100), new Error(
        `Failed to read file from blueprint. This Blueprint refers to a resource of type "bundled" with path "${this.resource.path}" that was not available. Please ensure that the entire bundle, such as a blueprint.zip file, is loaded. If you are trying to load the Blueprint directly from a URL or a local directory, make sure that all the necessary files are accessible and located relative to the blueprint file. 

Error details: ${u instanceof Error ? u.message : String(u)}`,
        { cause: u }
      );
    }
  }
  /** @inheritDoc */
  get name() {
    return this.resource.path.split("/").pop() || "";
  }
  /** @inheritDoc */
  get isAsync() {
    return !0;
  }
}
class xt extends we {
  constructor(t, n, l) {
    super(), this.reference = t, this.innerResource = n, this._progress = l;
  }
  /** @inheritDoc */
  async resolve() {
    var s, e;
    (s = this.progress) == null || s.setCaption(`Creating ZIP: ${this.name}`);
    const t = await this.innerResource.resolve();
    let n;
    t instanceof File ? n = [t] : n = Ct(t.files, t.name);
    const l = ft(n), u = await ut(this.name, l);
    return (e = this.progress) == null || e.set(100), u;
  }
  /** @inheritDoc */
  get name() {
    if (this.reference.name)
      return this.reference.name;
    const t = this.innerResource.name;
    return t.endsWith(".zip") ? t : `${t}.zip`;
  }
  /** @inheritDoc */
  get isAsync() {
    return !0;
  }
}
function Ct(r, t) {
  const n = [];
  function l(u, s) {
    for (const [e, p] of Object.entries(u)) {
      const d = s ? `${s}/${e}` : e;
      p instanceof Uint8Array ? n.push(new File([p], `${t}/${d}`)) : typeof p == "string" ? n.push(
        new File(
          [new TextEncoder().encode(p)],
          `${t}/${d}`
        )
      ) : l(p, d);
    }
  }
  return l(r, ""), n;
}
const Fr = async (r, { pluginPath: t, pluginName: n }, l) => {
  l == null || l.tracker.setCaption(`Activating ${n || t}`);
  const u = await r.documentRoot, s = await r.run({
    code: `<?php
			define( 'WP_ADMIN', true );
			require_once( getenv('DOCROOT') . "/wp-load.php" );
			require_once( getenv('DOCROOT') . "/wp-admin/includes/plugin.php" );

			// Set current user to admin
			wp_set_current_user( get_users(array('role' => 'Administrator') )[0]->ID );

			$plugin_path = getenv('PLUGIN_PATH');
			$response = false;
			if ( ! is_dir( $plugin_path)) {
				$response = activate_plugin($plugin_path);
			}

			// Activate plugin by name if activation by path wasn't successful
			if ( null !== $response ) {
				foreach ( ( glob( $plugin_path . '/*.php' ) ?: array() ) as $file ) {
					$info = get_plugin_data( $file, false, false );
					if ( ! empty( $info['Name'] ) ) {
						$response = activate_plugin( $file );
						break;
					}
				}
			}

			if ( is_wp_error($response) ) {
				die( $response->get_error_message() );
			} else if ( false === $response ) {
				die( "The activatePlugin step wasn't able to find the plugin $plugin_path." );
			}
		`,
    env: {
      PLUGIN_PATH: t,
      DOCROOT: u
    }
  });
  s.text && ae.warn(
    `Plugin ${t} activation printed the following bytes: ${s.text}`
  );
  const p = ((await r.run({
    code: `<?php
			ob_start();
			require_once( getenv( 'DOCROOT' ) . "/wp-load.php" );

			$plugin_directory = rtrim( WP_PLUGIN_DIR, '/' ) . '/';
			$relative_plugin_path = getenv( 'PLUGIN_PATH' );
			if (strpos($relative_plugin_path, $plugin_directory) === 0) {
				$relative_plugin_path = substr($relative_plugin_path, strlen($plugin_directory));
			}

			if ( is_dir( $plugin_directory . $relative_plugin_path ) ) {
				$relative_plugin_path = rtrim( $relative_plugin_path, '/' ) . '/';
			}

			$active_plugins = get_option( 'active_plugins' );
			if ( ! is_array( $active_plugins ) ) {
				$active_plugins = array();
			}
			ob_end_clean();

			/**
			 * Use a shutdown function to ensure the activation-related output comes
			 * last in stdout.
			 */
			register_shutdown_function( function() use ( $relative_plugin_path, $active_plugins ) {
				foreach ( $active_plugins as $plugin ) {
					if ( substr( $plugin, 0, strlen( $relative_plugin_path ) ) === $relative_plugin_path ) {
						die('{"success": true}');
						break;
					}
				}
				die('{"success": false}');
			});
		`,
    env: {
      DOCROOT: u,
      PLUGIN_PATH: t
    }
  })).text ?? "").trim();
  if (!p.endsWith('{"success": true}'))
    throw p !== '{"success": false}' && ae.debug(p), new Error(
      `Plugin ${t} could not be activated - WordPress exited with exit code ${s.exitCode}. Inspect the "debug" logs in the console for more details. Output headers: ${JSON.stringify(
        s.headers,
        null,
        2
      )}`
    );
}, Nr = async (r, { themeFolderName: t }, n) => {
  n == null || n.tracker.setCaption(`Activating ${t}`);
  const l = await r.documentRoot, u = `${l}/wp-content/themes/${t}`;
  if (!await r.fileExists(u))
    throw new Error(`
			Couldn't activate theme ${t}.
			Theme not found at the provided theme path: ${u}.
			Check the theme path to ensure it's correct.
			If the theme is not installed, you can install it using the installTheme step.
			More info can be found in the Blueprint documentation: https://wordpress.github.io/wordpress-playground/blueprints/steps/#ActivateThemeStep
		`);
  const s = await r.run({
    code: `<?php
			define( 'WP_ADMIN', true );
			require_once( getenv('docroot') . "/wp-load.php" );

			// Set current user to admin
			wp_set_current_user( get_users(array('role' => 'Administrator') )[0]->ID );

			switch_theme( getenv('themeFolderName') );

			if( wp_get_theme()->get_stylesheet() !== getenv('themeFolderName') ) {
				throw new Exception( 'Theme ' . getenv('themeFolderName') . ' could not be activated.' );				
			}
			die('Theme activated successfully');
		`,
    env: {
      docroot: l,
      themeFolderName: t
    }
  });
  if (s.text !== "Theme activated successfully")
    throw ae.debug(s), new Error(
      `Theme ${t} could not be activated - WordPress exited with exit code ${s.exitCode}. Inspect the "debug" logs in the console for more details. Output headers: ${JSON.stringify(
        s.headers,
        null,
        2
      )}`
    );
}, Dt = async (r, { code: t }) => {
  let n = typeof t == "string" ? t : t.content;
  return (n.includes('"wordpress/wp-load.php"') || n.includes("'wordpress/wp-load.php'")) && (ae.error(
    `
It looks like you're trying to load WordPress using a relative path 'wordpress/wp-load.php'.

Playground recently changed the working directory from '/' to '/wordpress' to better mimic
how real web servers work. This means relative paths that used to work may no longer
point to the correct location.

Playground automatically updated the path for you, but at one point path rewriting will be removed. Please
update your code to use an absolute path instead:

Instead of:  require_once 'wordpress/wp-load.php';
Use:         require_once '/wordpress/wp-load.php';

This will ensure your code works reliably regardless of the current working directory.
		`.trim()
  ), n = n.replace(
    "'wordpress/wp-load.php'",
    "'/wordpress/wp-load.php'"
  ), n = n.replace(
    '"wordpress/wp-load.php"',
    '"/wordpress/wp-load.php"'
  )), await r.run({ code: n });
}, Ut = async (r, { options: t }) => await r.run(t), cr = async (r, { path: t }) => {
  t.startsWith("/") || (ae.error(
    `
The rm() step in your Blueprint refers to a relative path.

Playground recently changed the working directory from '/' to '/wordpress' to better mimic 
how real web servers work. This means relative paths that used to work may no longer 
point to the correct location.

Playground automatically updated the path for you, but at one point path rewriting will be removed. Please
update your code to use an absolute path instead:

Instead of:  rm({ path: 'wordpress/wp-load.php' });
Use:         rm({ path: '/wordpress/wp-load.php' });

This will ensure your code works reliably regardless of the current working directory.
		`.trim()
  ), t = `/${t}`), await r.unlink(t);
}, It = `<?php

/**
 * Naively splits an SQL string into a sequence of queries. It
 * streams the data so you can process very large chunks of SQL
 * without running out of memory.
 * 
 * This class is **naive** because it doesn't understand what a
 * valid query is. The lexer does not provide a way to distinguish
 * between a syntax error and an incomplete input yet. Lacking this
 * information, we assume that no SQL query is larger than 2MB and,
 * failing to extract a query from a 2MB buffer, we fail. This heuristic
 * is often sufficient, but may fail in pathological cases.
 * 
 * Usage:
 * 
 *     $stream = new WP_MySQL_Naive_Query_Stream();
 *     $stream->append_sql( 'SELECT id FROM users; SELECT * FROM posts;' );
 *     while ( $stream->next_query() ) {
 *         $sql_string = $stream->get_query();
 *         // Process the query.
 *     }
 *     $stream->append_sql( 'CREATE TABLE users (id INT, name VARCHAR(255));' );
 *     while ( $stream->next_query() ) {
 *         $sql_string = $stream->get_query();
 *         // Process the query.
 *     }
 *     $stream->mark_input_complete();
 *     $stream->next_query(); // returns false
 */
class WP_MySQL_Naive_Query_Stream {

	private $sql_buffer = '';
	private $input_complete = false;
	private $state = true;
	private $last_query = false;

	const STATE_QUERY = 'valid';
	const STATE_SYNTAX_ERROR = 'syntax_error';
	const STATE_PAUSED_ON_INCOMPLETE_INPUT = 'paused_on_incomplete_input';
	const STATE_FINISHED = 'finished';

	/**
	 * The maximum size of the buffer to store the SQL input. We don't
	 * have enough information from the lexer to distinguish between
	 * an incomplete input and a syntax error so we use a heuristic –
	 * if we've accumulated more than this amount of SQL input, we assume
	 * it's a syntax error. That's why this class is called a "naive" query
	 * stream.
	 */
	const MAX_SQL_BUFFER_SIZE = 1024 * 1024 * 15;

	public function __construct() {}

	public function append_sql( string $sql ) {
		if($this->input_complete) {
			return false;
		}
		$this->sql_buffer .= $sql;
		$this->state = self::STATE_QUERY;
		return true;
	}

	public function is_paused_on_incomplete_input(): bool {
		return $this->state === self::STATE_PAUSED_ON_INCOMPLETE_INPUT;
	}

	public function mark_input_complete() {
		$this->input_complete = true;
	}

	public function next_query() {
		$this->last_query = false;
		if($this->state === self::STATE_PAUSED_ON_INCOMPLETE_INPUT) {
			return false;
		}

		$result = $this->do_next_query();
		if(!$result && strlen($this->sql_buffer) > self::MAX_SQL_BUFFER_SIZE) {
			$this->state = self::STATE_SYNTAX_ERROR;
			return false;
		}
		return $result;
	}

	private function do_next_query() {
		$query = [];
		$lexer = new WP_MySQL_Lexer( $this->sql_buffer );
		while ( $lexer->next_token() ) {
			$token = $lexer->get_token();
			$query[] = $token;
			if ( $token->id === WP_MySQL_Lexer::SEMICOLON_SYMBOL ) {
				// Got a complete query!
				break;
			}
		}

		// @TODO: expose this method from the lexer
		// if($lexer->get_state() === WP_MySQL_Lexer::STATE_SYNTAX_ERROR) {
		// 	return false;
		// }

		if(!count($query)) {
			if ( $this->input_complete ) {
				$this->state = self::STATE_FINISHED;
			} else {
				$this->state = self::STATE_PAUSED_ON_INCOMPLETE_INPUT;
			}
			return false;
		}

		// The last token either needs to end with a semicolon, or be the
		// last token in the input.
		$last_token = $query[count($query) - 1];
		if ( 
			$last_token->id !== WP_MySQL_Lexer::SEMICOLON_SYMBOL &&
			! $this->input_complete
		) {
			$this->state = self::STATE_PAUSED_ON_INCOMPLETE_INPUT;
			return false;
		}

		// See if the query has any meaningful tokens. We don't want to return
		// to give the caller a comment disguised as a query.
		$has_meaningful_tokens = false;
		foreach($query as $token) {
			if ( 
				$token->id !== WP_MySQL_Lexer::WHITESPACE && 
				$token->id !== WP_MySQL_Lexer::COMMENT &&
				$token->id !== WP_MySQL_Lexer::MYSQL_COMMENT_START &&
				$token->id !== WP_MySQL_Lexer::MYSQL_COMMENT_END &&
				$token->id !== WP_MySQL_Lexer::EOF
			) {
				$has_meaningful_tokens = true;
				break;
			}
		}
		if(!$has_meaningful_tokens) {
			if ( $this->input_complete ) {
				$this->state = self::STATE_FINISHED;
			} else {
				$this->state = self::STATE_PAUSED_ON_INCOMPLETE_INPUT;
			}
			return false;
		}

		// Remove the query from the input buffer and return it.
		$last_byte = $last_token->start + $last_token->length;
		$query = substr($this->sql_buffer, 0, $last_byte);
		$this->sql_buffer = substr($this->sql_buffer, $last_byte);
		$this->last_query = $query;
		$this->state = self::STATE_QUERY;
		return true;
	}

	public function get_query() {
		return $this->last_query;
	}

	public function get_state() {
		return $this->state;
	}

}`, Wt = async (r, { sql: t }, n) => {
  n == null || n.tracker.setCaption("Executing SQL Queries");
  const l = `/tmp/${ir()}.sql`, u = `/tmp/${ir()}.php`;
  await r.writeFile(
    l,
    new Uint8Array(await t.arrayBuffer())
  ), await r.writeFile(
    u,
    new TextEncoder().encode(It)
  );
  const s = await r.documentRoot, e = mr({ docroot: s, sqlFilename: l, streamClassFilename: u }), p = await r.run({
    code: `<?php
		define('WP_SQLITE_AST_DRIVER', true);
		require_once ${e.docroot} . '/wp-load.php';

		// Load WP_MySQL_Naive_Query_Stream from the bundled file
		require_once ${e.streamClassFilename};

		global $wpdb;

		do_action('run_sql_step');

		$stream = new WP_MySQL_Naive_Query_Stream();

		// Open the SQL file for streaming
		$handle = fopen(${e.sqlFilename}, 'r');
		if (!$handle) {
			throw new Exception('Failed to open SQL file');
		}

		// Read and process the file in 8KB chunks
		$chunk_size = 8192;
		while (!feof($handle)) {
			$chunk = fread($handle, $chunk_size);
			if ($chunk === false) {
				break;
			}

			$stream->append_sql($chunk);

			// Process any complete queries in the stream
			while ($stream->next_query()) {
				$query = $stream->get_query();
				$wpdb->query($query);
			}
		}

		fclose($handle);

		// Mark input as complete and process any remaining queries
		$stream->mark_input_complete();
		while ($stream->next_query()) {
			$query = $stream->get_query();
			$wpdb->query($query);
		}
	`
  });
  return await cr(r, { path: l }), await cr(r, { path: u }), p;
}, Bt = async (r, { request: t }) => {
  ae.warn(
    'Deprecated: The Blueprint step "request" is deprecated and will be removed in a future release.'
  );
  const n = await r.request(t);
  if (n.httpStatusCode > 399 || n.httpStatusCode < 200)
    throw ae.warn("WordPress response was", { response: n }), new Error(
      `Request failed with status ${n.httpStatusCode}`
    );
  return n;
}, br = async (r, { consts: t, method: n = "define-before-run" }) => {
  switch (n) {
    case "define-before-run":
      await Mt(r, t);
      break;
    case "rewrite-wp-config": {
      const l = await r.documentRoot, u = ie(l, "/wp-config.php");
      await ct(r, u, t);
      break;
    }
    default:
      throw new Error(`Invalid method: ${n}`);
  }
};
async function Mt(r, t) {
  for (const n in t)
    await r.defineConstant(n, t[n]);
}
const xr = async (r, { options: t }) => {
  const n = await r.documentRoot;
  await r.run({
    code: `<?php
		include ${be(n)} . '/wp-load.php';
		$site_options = ${be(t)};
		foreach($site_options as $name => $value) {
			update_option($name, $value);
		}
		echo "Success";
		`
  });
}, zt = async (r, { meta: t, userId: n }) => {
  const l = await r.documentRoot;
  await r.run({
    code: `<?php
		include ${be(l)} . '/wp-load.php';
		$meta = ${be(t)};
		foreach($meta as $name => $value) {
			update_user_meta(${be(n)}, $name, $value);
		}
		`
  });
}, wr = "/tmp/wp-cli.phar", Vt = {
  resource: "url",
  /**
   * Use compression for downloading the wp-cli.phar file.
   * The official release, hosted at raw.githubusercontent.com, is ~7MB
   * and the transfer is uncompressed. playground.wordpress.net supports
   * transfer compression and only transmits ~1.4MB.
   *
   * @TODO: minify the wp-cli.phar file. It can be as small as 1MB when all the
   *        whitespaces and are removed, and even 500KB when libraries
   *        like the JavaScript parser or Composer are removed.
   */
  url: "https://playground.wordpress.net/wp-cli.phar"
}, Cr = async (r, t = wr) => {
  if (!await r.fileExists(t))
    throw new Error(`wp-cli.phar not found at ${t}.
			You can enable wp-cli support by adding "wp-cli" to the list of extra libraries in your blueprint as follows:
			{
				"extraLibraries": [ "wp-cli" ]
			}
			Read more about it in the documentation.
			https://wordpress.github.io/wordpress-playground/blueprints/data-format#extra-libraries`);
}, Dr = async (r, { command: t, wpCliPath: n = wr }) => {
  await Cr(r, n);
  let l;
  if (typeof t == "string" ? (t = t.trim(), l = Ht(t)) : l = t, l.shift() !== "wp")
    throw new Error('The first argument must be "wp".');
  let s = !1;
  const e = l.map((y) => y.startsWith("wordpress/") ? (s = !0, `/${y}`) : y);
  s && ae.error(
    `
The wp-cli step in your Blueprint refers to a relative path.

Playground recently changed the working directory from '/' to '/wordpress' to better mimic 
how real web servers work. This means relative paths that used to work may no longer 
point to the correct location.

Playground automatically updated the path for you, but at one point path rewriting will be removed. Please
update your code to use an absolute path instead:

Instead of:

        {
            "step": "wp-cli",
            "command": "wp media import wordpress/wp-content/Select-storage-method.png --post_id=4 --title='Select your storage method' --featured_image"
        }

Use:

        {
            "step": "wp-cli",
            "command": "wp media import /wordpress/wp-content/Select-storage-method.png --post_id=4 --title='Select your storage method' --featured_image"
        }

This will ensure your code works reliably regardless of the current working directory.
        `.trim()
  );
  const p = await r.documentRoot;
  await r.writeFile("/tmp/stdout", ""), await r.writeFile("/tmp/stderr", ""), await r.writeFile(
    ie(p, "run-cli.php"),
    `<?php
		// Set up the environment to emulate a shell script
		// call.

		// Set SHELL_PIPE to 0 to ensure WP-CLI formats
		// the output as ASCII tables.
		// @see https://github.com/wp-cli/wp-cli/issues/1102
		putenv( 'SHELL_PIPE=0' );

		// Set the argv global.
		$GLOBALS['argv'] = array_merge([
		  "/tmp/wp-cli.phar",
		  "--path=${p}"
		], ${be(e)});

		// Provide stdin, stdout, stderr streams outside of
		// the CLI SAPI.
		define('STDIN', fopen('php://stdin', 'rb'));
		define('STDOUT', fopen('php://stdout', 'wb'));
		define('STDERR', fopen('php://stderr', 'wb'));

		require( ${be(n)} );
		`
  );
  const d = await r.run({
    scriptPath: ie(p, "run-cli.php")
  });
  if (d.exitCode !== 0)
    throw new Error(d.errors);
  return d;
};
function Ht(r) {
  let l = 0, u = "";
  const s = [];
  let e = "";
  for (let p = 0; p < r.length; p++) {
    const d = r[p];
    l === 0 ? d === '"' || d === "'" ? (l = 1, u = d) : d.match(/\s/) ? (e && s.push(e), e = "") : e += d : l === 1 && (d === "\\" ? (p++, e += r[p]) : d === u ? (l = 0, u = "") : e += d);
  }
  return e && s.push(e), s;
}
const Zt = async (r, { wpCliPath: t }) => {
  await Cr(r, t), await br(r, {
    consts: {
      WP_ALLOW_MULTISITE: 1
    }
  });
  const n = new URL(await r.absoluteUrl);
  if (n.port !== "") {
    let y = `The current host is ${n.host}, but WordPress multisites do not support custom ports.`;
    throw n.hostname === "localhost" && (y += " For development, you can set up a playground.test domain using the instructions at https://wordpress.github.io/wordpress-playground/contributing/code."), new Error(y);
  }
  const l = n.pathname.replace(/\/$/, "") + "/", u = `${n.protocol}//${n.hostname}${l}`;
  await xr(r, {
    options: {
      siteurl: u,
      home: u
    }
  }), await Dr(r, {
    command: `wp core multisite-convert --base="${l}"`
  });
  const e = `${await r.documentRoot}/wp-config.php`, p = await r.readFileAsText(e);
  let d = p;
  p.includes("$_SERVER['HTTP_HOST']") || (d = p.replace(
    /^<\?php\s*/i,
    `<?php
$_SERVER['HTTP_HOST'] = ${be(n.hostname)};
`
  )), await r.writeFile(e, d);
}, Qt = async (r, { fromPath: t, toPath: n }) => {
  (!t.startsWith("/") || !n.startsWith("/")) && ae.error(
    `
The cp() step in your Blueprint refers to a relative path.

Playground recently changed the working directory from '/' to '/wordpress' to better mimic 
how real web servers work. This means relative paths that used to work may no longer 
point to the correct location.

Playground automatically updated the path for you, but at one point path rewriting will be removed. Please
update your code to use an absolute path instead:

Instead of:  cp({ fromPath: 'wordpress/wp-load.php', toPath: 'wordpress/wp-load.php' });
Use:         cp({ fromPath: '/wordpress/wp-load.php', toPath: '/wordpress/wp-load.php' });

This will ensure your code works reliably regardless of the current working directory.
		`.trim()
  ), t.startsWith("/") || (t = `/${t}`), n.startsWith("/") || (n = `/${n}`), await r.writeFile(
    n,
    await r.readFileAsBuffer(t)
  );
}, Yt = async (r, { fromPath: t, toPath: n }) => {
  (!t.startsWith("/") || !n.startsWith("/")) && ae.error(
    `
The mv() step in your Blueprint refers to a relative path.

Playground recently changed the working directory from '/' to '/wordpress' to better mimic 
how real web servers work. This means relative paths that used to work may no longer 
point to the correct location.

Playground automatically updated the path for you, but at one point path rewriting will be removed. Please
update your code to use an absolute path instead:

Instead of:  mv({ fromPath: 'wordpress/wp-load.php', toPath: 'wordpress/wp-load.php' });
Use:         mv({ fromPath: '/wordpress/wp-load.php', toPath: '/wordpress/wp-load.php' });

This will ensure your code works reliably regardless of the current working directory.
		`.trim()
  ), t.startsWith("/") || (t = `/${t}`), n.startsWith("/") || (n = `/${n}`), await r.mv(t, n);
}, Gt = async (r, { path: t }) => {
  t.startsWith("/") || ae.error(
    `
The mkdir() step in your Blueprint refers to a relative path.

Playground recently changed the working directory from '/' to '/wordpress' to better mimic 
how real web servers work. This means relative paths that used to work may no longer 
point to the correct location.

Playground automatically updated the path for you, but at one point path rewriting will be removed. Please
update your code to use an absolute path instead:

Instead of:  mkdir({ path: 'wordpress/my-new-folder' });
Use:         mkdir({ path: '/wordpress/my-new-folder' });

This will ensure your code works reliably regardless of the current working directory.
		`.trim()
  ), await r.mkdir(t);
}, Jt = async (r, { path: t }) => {
  t.startsWith("/") || (ae.error(
    `
The rmdir() step in your Blueprint refers to a relative path.

Playground recently changed the working directory from '/' to '/wordpress' to better mimic 
how real web servers work. This means relative paths that used to work may no longer 
point to the correct location.

Playground automatically updated the path for you, but at one point path rewriting will be removed. Please
update your code to use an absolute path instead:

Instead of:  rmdir({ path: 'wordpress/wp-load.php' });
Use:         rmdir({ path: '/wordpress/wp-load.php' });

This will ensure your code works reliably regardless of the current working directory.
		`.trim()
  ), t = `/${t}`), await r.rmdir(t);
}, vr = async (r, { path: t, data: n }) => {
  n instanceof File && (n = new Uint8Array(await n.arrayBuffer())), t.startsWith("/") || (ae.error(
    `
The writeFile() step in your Blueprint refers to a relative path.

Playground recently changed the working directory from '/' to '/wordpress' to better mimic 
how real web servers work. This means relative paths that used to work may no longer 
point to the correct location.

Playground automatically updated the path for you, but at one point path rewriting will be removed. Please
update your code to use an absolute path instead:

Instead of:  writeFile({ path: 'wordpress/wp-load.php', data: '<?php echo "Hello World!"; ?>' });
Use:         writeFile({ path: '/wordpress/wp-load.php', data: '<?php echo "Hello World!"; ?>' });

This will ensure your code works reliably regardless of the current working directory.
		`.trim()
  ), t = `/${t}`), t.startsWith("/wordpress/wp-content/mu-plugins") && !await r.fileExists("/wordpress/wp-content/mu-plugins") && await r.mkdir("/wordpress/wp-content/mu-plugins"), await r.writeFile(t, n);
}, Xt = async (r, { writeToPath: t, filesTree: n }) => {
  t.startsWith("/") || (ae.error(
    `
The writeFiles() step in your Blueprint refers to a relative path.

Playground recently changed the working directory from '/' to '/wordpress' to better mimic 
how real web servers work. This means relative paths that used to work may no longer
point to the correct location.

Playground automatically updated the path for you, but at one point path rewriting will be removed. Please
update your code to use an absolute path instead:

Instead of:  writeFiles({ writeToPath: 'wordpress/wp-content/plugins/my-plugin', filesTree: { name: 'style.css': 'a { color: red; }' });
Use:         writeFiles({ writeToPath: '/wordpress/wp-content/plugins/my-plugin', filesTree: { name: 'style.css': 'a { color: red; }' });

This will ensure your code works reliably regardless of the current working directory.
		`.trim()
  ), t = `/${t}`), await hr(r, t, n.files);
}, Ur = async (r, { siteUrl: t }) => {
  await br(r, {
    consts: {
      WP_HOME: t,
      WP_SITEURL: t
    }
  });
}, Kt = async (r, { file: t }, n) => {
  await es(r, t, n);
};
async function es(r, t, n) {
  var l;
  (l = n == null ? void 0 : n.tracker) == null || l.setCaption("Importing content"), await vr(r, {
    path: "/tmp/import.wxr",
    data: t
  }), await r.run({
    $_SERVER: {
      /**
       * get_site_url() infers the protocol from $_SERVER['HTTPS'] instead of
       * using the stored siteurl option. The importer relies on that behavior
       * when rewriting links in the WXR payload, so we populate the flag here
       * just as the web request layer would.
       */
      HTTPS: (await r.absoluteUrl).startsWith("https://") ? "on" : ""
    },
    code: `<?php
	define('WP_LOAD_IMPORTERS', true);
	require 'wp-load.php';
	require 'wp-admin/includes/admin.php';

	/**
	 * Disable all kses filters to prevent content sanitization during import.
	 * It messes up Playground URL scheme by mangling transforming code such as:
	 *
	 *     <a href="/scope:kind-quiet-lake/index.php">Test</a>
	 *
	 * into:
	 *
	 *     <a href="kind-quiet-lake/index.php">Test</a>
	 */
	kses_remove_filters();

	// Set current user for the importer to pick it up as the default
	// post author.
	$admin_id = get_users(array('role' => 'Administrator') )[0]->ID;
	wp_set_current_user( $admin_id );

	$wp_import                  = new WP_Import();
	$import_data                = $wp_import->parse( getenv('IMPORT_FILE') );

	// Prepare the data to be used in process_author_mapping();
	$wp_import->get_authors_from_import( $import_data );

	// We no longer need the original data, so unset to avoid using excess
	// memory.
	unset( $import_data );

	// Drive the import
	$wp_import->fetch_attachments = getenv('FETCH_ATTACHMENTS') === 'true';

	$_GET  = array(
		'import' => 'wordpress',
		'step'   => 2,
	);
	$_POST = array(
		'imported_authors'  => array(),
		'user_map'          => array(),
		'fetch_attachments' => $wp_import->fetch_attachments,
	);

	$GLOBALS['wpcli_import_current_file'] = basename( $file );
	$wp_import->import( getenv('IMPORT_FILE'), [
		'rewrite_urls' => true,
	] );
	`,
    env: {
      IMPORT_FILE: "/tmp/import.wxr",
      FETCH_ATTACHMENTS: "true"
    }
  });
}
const Ir = async (r, { themeSlug: t = "" }, n) => {
  var u;
  (u = n == null ? void 0 : n.tracker) == null || u.setCaption("Importing theme starter content");
  const l = await r.documentRoot;
  await r.run({
    code: `<?php

		/**
		 * Ensure that the customizer loads as an admin user.
		 *
		 * For compatibility with themes, this MUST be run prior to theme inclusion, which is why this is a plugins_loaded filter instead
		 * of running _wp_customize_include() manually after load.
		 */
		function importThemeStarterContent_plugins_loaded() {
			// Set as the admin user, this ensures we can customize the site.
			wp_set_current_user(
				get_users( [ 'role' => 'Administrator' ] )[0]
			);

			// Force the site to be fresh, although it should already be.
			add_filter( 'pre_option_fresh_site', '__return_true' );

			/*
			 * Simulate this request as the customizer loading with the current theme in preview mode.
			 *
			 * See _wp_customize_include()
			 */
			$_REQUEST['wp_customize']    = 'on';
			$_REQUEST['customize_theme'] = ${be(t)} ?: get_stylesheet();

			/*
			 * Claim this is a ajax request saving settings, to avoid the preview filters being applied.
			 */
			$_REQUEST['action'] = 'customize_save';
			add_filter( 'wp_doing_ajax', '__return_true' );

			$_GET = $_REQUEST;
		}
		playground_add_filter( 'plugins_loaded', 'importThemeStarterContent_plugins_loaded', 0 );

		require ${be(l)} . '/wp-load.php';

		// Return early if there's no starter content.
		if ( ! get_theme_starter_content() ) {
			return;
		}

		// Import the Starter Content.
		$wp_customize->import_theme_starter_content();

		// Publish the changeset, which publishes the starter content.
		wp_publish_post( $wp_customize->changeset_post_id() );
		`
  });
}, Pr = async (r, { zipFile: t, zipPath: n, extractToPath: l }) => {
  if (n)
    ae.warn(
      'The "zipPath" option of the unzip() Blueprint step is deprecated and will be removed. Use "zipFile" instead.'
    );
  else if (!t)
    throw new Error("Either zipPath or zipFile must be provided");
  await Ar(r, t || n, l);
}, Wr = [
  "db.php",
  "plugins/akismet",
  "plugins/hello.php",
  "plugins/wordpress-importer",
  "mu-plugins/sqlite-database-integration",
  "mu-plugins/playground-includes",
  "mu-plugins/0-playground.php",
  "mu-plugins/0-sqlite.php",
  /*
   * Listing core themes like that here isn't ideal, especially since
   * developers may actually want to use one of them.
   * @TODO Let's give the user a choice whether or not to include them.
   */
  "themes/twentytwenty",
  "themes/twentytwentyone",
  "themes/twentytwentytwo",
  "themes/twentytwentythree",
  "themes/twentytwentyfour",
  "themes/twentytwentyfive",
  "themes/twentytwentysix"
], rs = async (r, { wordPressFilesZip: t, pathInZip: n = "" }) => {
  const l = await r.documentRoot;
  let u = ie("/tmp", "import");
  await r.mkdir(u), await Pr(r, {
    zipFile: t,
    extractToPath: u
  }), u = ie(u, n);
  const s = ie(u, "playground-export.json");
  let e = null;
  if (await r.fileExists(s))
    try {
      const E = await r.readFileAsText(s);
      e = JSON.parse(E).siteUrl, await r.unlink(s);
    } catch {
    }
  const p = ie(u, "wp-content"), d = ie(l, "wp-content");
  for (const E of Wr) {
    const $ = ie(
      p,
      E
    );
    await Er(r, $);
    const g = ie(d, E);
    await r.fileExists(g) && (await r.mkdir(yr($)), await r.mv(g, $));
  }
  const y = ie(
    u,
    "wp-content",
    "database"
  );
  await r.fileExists(y) || await r.mv(
    ie(l, "wp-content", "database"),
    y
  );
  const _ = await r.listFiles(u);
  for (const E of _)
    await Er(r, ie(l, E)), await r.mv(
      ie(u, E),
      ie(l, E)
    );
  await r.rmdir(u), await mt(r, l);
  const h = await r.absoluteUrl;
  e || (e = await ss(r, l)), await Ur(r, {
    siteUrl: h
  });
  const k = be(
    ie(l, "wp-admin", "upgrade.php")
  );
  await r.run({
    code: `<?php
            $_GET['step'] = 'upgrade_db';
            require ${k};
            `
  }), e && e !== h && await ts(r, l, e, h);
};
function Tr(r) {
  const t = r.match(/\/scope:[^/]+\/?/);
  return t ? t[0].replace(/\/?$/, "/") : null;
}
async function ts(r, t, n, l) {
  const u = Tr(n), s = Tr(l);
  !u || !s || u !== s && await r.run({
    code: `<?php
		require_once getenv('DOCUMENT_ROOT') . '/wp-load.php';
		global $wpdb;

		$old_scope = getenv('OLD_SCOPE');
		$new_scope = getenv('NEW_SCOPE');

		// Update URLs in posts content, excerpts, and GUIDs
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
			$old_scope, $new_scope
		));
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->posts} SET post_excerpt = REPLACE(post_excerpt, %s, %s)",
			$old_scope, $new_scope
		));
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->posts} SET guid = REPLACE(guid, %s, %s)",
			$old_scope, $new_scope
		));

		// Update URLs in post meta
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
			$old_scope, $new_scope, '%' . $wpdb->esc_like($old_scope) . '%'
		));

		// Update URLs in options (handles both regular and serialized data)
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->options} SET option_value = REPLACE(option_value, %s, %s) WHERE option_value LIKE %s",
			$old_scope, $new_scope, '%' . $wpdb->esc_like($old_scope) . '%'
		));

		// Update URLs in user meta
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->usermeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
			$old_scope, $new_scope, '%' . $wpdb->esc_like($old_scope) . '%'
		));

		// Update URLs in term meta
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->termmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
			$old_scope, $new_scope, '%' . $wpdb->esc_like($old_scope) . '%'
		));

		// Update URLs in comments
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->comments} SET comment_content = REPLACE(comment_content, %s, %s) WHERE comment_content LIKE %s",
			$old_scope, $new_scope, '%' . $wpdb->esc_like($old_scope) . '%'
		));
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->comments} SET comment_author_url = REPLACE(comment_author_url, %s, %s) WHERE comment_author_url LIKE %s",
			$old_scope, $new_scope, '%' . $wpdb->esc_like($old_scope) . '%'
		));
		`,
    env: {
      DOCUMENT_ROOT: t,
      OLD_SCOPE: u,
      NEW_SCOPE: s
    }
  });
}
async function ss(r, t) {
  const n = mr({ documentRoot: t });
  return (await r.run({
    code: `<?php
		require_once ${n.documentRoot} . '/wp-load.php';
		global $wpdb;
		$row = $wpdb->get_row("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'siteurl'");
		echo $row ? $row->option_value : '';
		`
  })).text.trim() || null;
}
async function Er(r, t) {
  await r.fileExists(t) && (await r.isDir(t) ? await r.rmdir(t) : await r.unlink(t));
}
async function is(r) {
  const t = await r.request({
    url: "/wp-admin/export.php?download=true&content=all"
  });
  return new File([t.bytes], "export.xml");
}
async function Br(r, {
  targetPath: t,
  zipFile: n,
  ifAlreadyInstalled: l = "overwrite",
  targetFolderName: u = ""
}) {
  const e = n.name.replace(/\.zip$/, ""), p = ie(await r.documentRoot, "wp-content"), d = ie(p, ir()), y = ie(d, "assets", e);
  await r.fileExists(y) && await r.rmdir(d, {
    recursive: !0
  }), await r.mkdir(d);
  try {
    await Pr(r, {
      zipFile: n,
      extractToPath: y
    });
    let _ = await r.listFiles(y, {
      prependPath: !0
    });
    _ = _.filter((g) => !g.endsWith("/__MACOSX"));
    const h = _.length === 1 && await r.isDir(_[0]);
    let k, E = "";
    h ? (E = _[0], k = _[0].split("/").pop()) : (E = y, k = e), u && u.length && (k = u);
    const $ = `${t}/${k}`;
    if (await r.fileExists($)) {
      if (!await r.isDir($))
        throw new Error(
          `Cannot install asset ${k} to ${$} because a file with the same name already exists. Note it's a file, not a directory! Is this by mistake?`
        );
      if (l === "overwrite")
        await r.rmdir($, {
          recursive: !0
        });
      else {
        if (l === "skip")
          return {
            assetFolderPath: $,
            assetFolderName: k
          };
        throw new Error(
          `Cannot install asset ${k} to ${t} because it already exists and the ifAlreadyInstalled option was set to ${l}`
        );
      }
    }
    return await r.mv(E, $), {
      assetFolderPath: $,
      assetFolderName: k
    };
  } finally {
    await r.rmdir(d, {
      recursive: !0
    });
  }
}
const os = async (r, { pluginData: t, pluginZipFile: n, ifAlreadyInstalled: l, options: u = {} }, s) => {
  n && (t = n, ae.warn(
    'The "pluginZipFile" option is deprecated. Use "pluginData" instead.'
  ));
  const e = ie(
    await r.documentRoot,
    "wp-content",
    "plugins"
  ), p = "targetFolderName" in u ? u.targetFolderName : "";
  let d = "", y = "";
  const _ = async (k) => {
    if (k.name.toLowerCase().endsWith(".zip"))
      return !0;
    const E = new Uint8Array(await k.arrayBuffer(), 0, 4);
    return E[0] === 80 && E[1] === 75 && E[2] === 3 && E[3] === 4;
  };
  if (t instanceof File)
    if (await _(t)) {
      const k = t.name.split("/").pop() || "plugin.zip";
      y = ar(k), s == null || s.tracker.setCaption(
        `Installing the ${y} plugin`
      );
      const E = await Br(r, {
        ifAlreadyInstalled: l,
        zipFile: t,
        targetPath: `${await r.documentRoot}/wp-content/plugins`,
        targetFolderName: p
      });
      d = E.assetFolderPath, y = E.assetFolderName;
    } else if (t.name.endsWith(".php")) {
      const k = ie(
        e,
        t.name
      );
      await vr(r, {
        path: k,
        data: t
      }), d = e, y = t.name;
    } else
      throw new Error(
        "pluginData looks like a file but does not look like a .zip or .php file."
      );
  else if (t) {
    y = t.name, s == null || s.tracker.setCaption(`Installing the ${y} plugin`);
    const k = ie(
      e,
      p || t.name
    );
    await hr(r, k, t.files, {
      rmRoot: !0
    }), d = k;
  }
  ("activate" in u ? u.activate : !0) && await Fr(
    r,
    {
      pluginPath: d,
      pluginName: y
    },
    s
  );
}, as = async (r, { themeData: t, themeZipFile: n, ifAlreadyInstalled: l, options: u = {} }, s) => {
  n && (t = n, ae.warn(
    'The "themeZipFile" option is deprecated. Use "themeData" instead.'
  ));
  const e = "targetFolderName" in u ? u.targetFolderName : "";
  let p = "", d = "";
  if (t instanceof File) {
    const h = t.name.split("/").pop() || "theme.zip";
    d = ar(h), s == null || s.tracker.setCaption(`Installing the ${d} theme`), p = (await Br(r, {
      ifAlreadyInstalled: l,
      zipFile: t,
      targetPath: `${await r.documentRoot}/wp-content/themes`,
      targetFolderName: e
    })).assetFolderName;
  } else {
    d = t.name, p = e || d, s == null || s.tracker.setCaption(`Installing the ${d} theme`);
    const h = ie(
      await r.documentRoot,
      "wp-content",
      "themes",
      p
    );
    await hr(r, h, t.files, {
      rmRoot: !0
    });
  }
  ("activate" in u ? u.activate : !0) && await Nr(
    r,
    {
      themeFolderName: p
    },
    s
  ), ("importStarterContent" in u ? u.importStarterContent : !1) && await Ir(
    r,
    {
      themeSlug: p
    },
    s
  );
}, ns = async (r, { username: t = "admin" } = {}, n) => {
  n == null || n.tracker.setCaption((n == null ? void 0 : n.initialCaption) || "Logging in"), r.defineConstant("PLAYGROUND_AUTO_LOGIN_AS_USER", t);
}, ps = async (r, t, n) => {
  var u;
  (u = n == null ? void 0 : n.tracker) == null || u.setCaption("Resetting WordPress data");
  const l = await r.documentRoot;
  await r.run({
    env: {
      DOCROOT: l
    },
    code: `<?php
		require getenv('DOCROOT') . '/wp-load.php';

		$GLOBALS['@pdo']->query('DELETE FROM wp_posts WHERE id > 0');
		$GLOBALS['@pdo']->query("UPDATE SQLITE_SEQUENCE SET SEQ=0 WHERE NAME='wp_posts'");
		
		$GLOBALS['@pdo']->query('DELETE FROM wp_postmeta WHERE post_id > 1');
		$GLOBALS['@pdo']->query("UPDATE SQLITE_SEQUENCE SET SEQ=20 WHERE NAME='wp_postmeta'");

		$GLOBALS['@pdo']->query('DELETE FROM wp_comments');
		$GLOBALS['@pdo']->query("UPDATE SQLITE_SEQUENCE SET SEQ=0 WHERE NAME='wp_comments'");

		$GLOBALS['@pdo']->query('DELETE FROM wp_commentmeta');
		$GLOBALS['@pdo']->query("UPDATE SQLITE_SEQUENCE SET SEQ=0 WHERE NAME='wp_commentmeta'");
		`
  });
}, ls = async (r, { options: t }) => {
  await r.request({
    url: "/wp-admin/install.php?step=2",
    method: "POST",
    body: {
      language: "en",
      prefix: "wp_",
      weblog_title: "My WordPress Website",
      user_name: t.adminPassword || "admin",
      admin_password: t.adminPassword || "password",
      // The installation wizard demands typing the same password twice
      admin_password2: t.adminPassword || "password",
      Submit: "Install WordPress",
      pw_weak: "1",
      admin_email: "admin@localhost.com"
    }
  });
}, fs = async (r, { selfContained: t = !1 } = {}) => {
  const n = "/tmp/wordpress-playground.zip", l = "/tmp/playground-export.json", u = await r.documentRoot, s = ie(u, "wp-content"), e = await r.absoluteUrl;
  await r.writeFile(
    l,
    new TextEncoder().encode(JSON.stringify({ siteUrl: e }))
  );
  let p = Wr;
  t && (p = p.filter((h) => !h.startsWith("themes/twenty")).filter(
    (h) => h !== "mu-plugins/sqlite-database-integration"
  ));
  const d = {
    [l]: "playground-export.json"
  };
  t && (d[ie(u, "wp-config.php")] = "wp-config.php");
  const y = mr({
    zipPath: n,
    wpContentPath: s,
    documentRoot: u,
    exceptPaths: p.map(
      (h) => ie(u, "wp-content", h)
    ),
    additionalPaths: d
  });
  await ds(
    r,
    `zipDir(${y.wpContentPath}, ${y.zipPath}, array(
			'exclude_paths' => ${y.exceptPaths},
			'zip_root'      => ${y.documentRoot},
			'additional_paths' => ${y.additionalPaths}
		));`
  );
  const _ = await r.readFileAsBuffer(n);
  return r.unlink(n), r.unlink(l), _;
}, us = `<?php

function zipDir($root, $output, $options = array())
{
    $root = rtrim($root, '/');
    $additionalPaths = array_key_exists('additional_paths', $options) ? $options['additional_paths'] : array();
    $excludePaths = array_key_exists('exclude_paths', $options) ? $options['exclude_paths'] : array();
    $zip_root = array_key_exists('zip_root', $options) ? $options['zip_root'] : $root;

    $zip = new ZipArchive;
    $res = $zip->open($output, ZipArchive::CREATE);
    if ($res === TRUE) {
        $directories = array(
            $root . '/'
        );
        while (sizeof($directories)) {
            $current_dir = array_pop($directories);

            if ($handle = opendir($current_dir)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry == '.' || $entry == '..') {
                        continue;
                    }

                    $entry = join_paths($current_dir, $entry);
                    if (in_array($entry, $excludePaths)) {
                        continue;
                    }

                    if (is_dir($entry)) {
                        $directory_path = $entry . '/';
                        array_push($directories, $directory_path);
                    } else if (is_file($entry)) {
                        // ensure compliance with zip spec by only using relative paths for files
                        $zip->addFile($entry, ltrim(substr($entry, strlen($zip_root)), '/'));
                    }
                }
                closedir($handle);
            }
        }
        foreach ($additionalPaths as $disk_path => $zip_path) {
            $zip->addFile($disk_path, $zip_path);
        }
        $zip->close();
        chmod($output, 0777);
    }
}

function join_paths()
{
    $paths = array();

    foreach (func_get_args() as $arg) {
        if ($arg !== '') {
            $paths[] = $arg;
        }
    }

    return preg_replace('#/+#', '/', join('/', $paths));
}
`;
async function ds(r, t) {
  return await r.run({
    code: us + t
  });
}
const cs = async (r, t) => {
  const u = (await (await fetch(
    `https://api.wordpress.org/translations/core/1.0/?version=${r}`
  )).json()).translations.find(
    (s) => s.language.toLowerCase() === t.toLowerCase()
  );
  if (!u)
    throw new Error(
      `Failed to get ${t} translation package for WordPress ${r}.`
    );
  return u.package;
}, ms = async (r, { language: t }, n) => {
  n == null || n.tracker.setCaption((n == null ? void 0 : n.initialCaption) || "Translating"), await r.defineConstant("WPLANG", t);
  const l = await r.documentRoot, u = (await r.run({
    code: `<?php
			require '${l}/wp-includes/version.php';
			echo $wp_version;
		`
  })).text, s = [
    {
      url: await cs(u, t),
      type: "core"
    }
  ], p = (await r.run({
    code: `<?php
		require_once('${l}/wp-load.php');
		require_once('${l}/wp-admin/includes/plugin.php');
		echo json_encode(
			array_values(
				array_map(
					function($plugin) {
						return [
							'slug'    => $plugin['TextDomain'],
							'version' => $plugin['Version']
						];
					},
					array_filter(
						get_plugins(),
						function($plugin) {
							return !empty($plugin['TextDomain']);
						}
					)
				)
			)
		);`
  })).json;
  for (const { slug: k, version: E } of p)
    s.push({
      url: `https://downloads.wordpress.org/translation/plugin/${k}/${E}/${t}.zip`,
      type: "plugin"
    });
  const y = (await r.run({
    code: `<?php
		require_once('${l}/wp-load.php');
		require_once('${l}/wp-admin/includes/theme.php');
		echo json_encode(
			array_values(
				array_map(
					function($theme) {
						return [
							'slug'    => $theme->get('TextDomain'),
							'version' => $theme->get('Version')
						];
					},
					wp_get_themes()
				)
			)
		);`
  })).json;
  for (const { slug: k, version: E } of y)
    s.push({
      url: `https://downloads.wordpress.org/translation/theme/${k}/${E}/${t}.zip`,
      type: "theme"
    });
  await r.isDir(`${l}/wp-content/languages/plugins`) || await r.mkdir(`${l}/wp-content/languages/plugins`), await r.isDir(`${l}/wp-content/languages/themes`) || await r.mkdir(`${l}/wp-content/languages/themes`);
  const _ = new qr({ concurrency: 5 }), h = s.map(
    ({ url: k, type: E }) => _.run(async () => {
      try {
        const $ = await fetch(k);
        if (!$.ok)
          throw new Error(
            `Failed to download translations for ${E}: ${$.statusText}`
          );
        let g = `${l}/wp-content/languages`;
        E === "plugin" ? g += "/plugins" : E === "theme" && (g += "/themes"), await Ar(
          r,
          new File(
            [await $.arrayBuffer()],
            `${t}-${E}.zip`
          ),
          g
        );
      } catch ($) {
        if (E === "core")
          throw new Error(
            `Failed to download translations for WordPress. Please check if the language code ${t} is correct. You can find all available languages and translations on https://translate.wordpress.org/.`
          );
        ae.warn(
          `Error downloading translations for ${E}: ${$}`
        );
      }
    })
  );
  await Promise.all(h);
}, ys = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  activatePlugin: Fr,
  activateTheme: Nr,
  cp: Qt,
  defineSiteUrl: Ur,
  defineWpConfigConsts: br,
  enableMultisite: Zt,
  exportWXR: is,
  importThemeStarterContent: Ir,
  importWordPressFiles: rs,
  importWxr: Kt,
  installPlugin: os,
  installTheme: as,
  login: ns,
  mkdir: Gt,
  mv: Yt,
  request: Bt,
  resetData: ps,
  rm: cr,
  rmdir: Jt,
  runPHP: Dt,
  runPHPWithOptions: Ut,
  runSql: Wt,
  runWpInstallationWizard: ls,
  setSiteLanguage: ms,
  setSiteOptions: xr,
  unzip: Pr,
  updateUserMeta: zt,
  wpCLI: Dr,
  writeFile: vr,
  writeFiles: Xt,
  zipWpContent: fs
}, Symbol.toStringTag, { value: "Module" })), hs = {
  properties: {
    landingPage: {
      type: "string",
      description: "The URL to navigate to after the blueprint has been run."
    },
    description: {
      type: "string",
      description: "Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what.",
      deprecated: "Use meta.description instead."
    },
    meta: {
      type: "object",
      properties: {
        title: {
          type: "string",
          description: "A clear and concise name for your Blueprint."
        },
        description: {
          type: "string",
          description: "A brief explanation of what your Blueprint offers."
        },
        author: {
          type: "string",
          description: "A GitHub username of the author of this Blueprint."
        },
        categories: {
          type: "array",
          items: { type: "string" },
          description: "Relevant categories to help users find your Blueprint in the future Blueprints section on WordPress.org."
        }
      },
      required: ["title", "author"],
      additionalProperties: !1,
      description: "Optional metadata. Used by the Blueprints gallery at https://github.com/WordPress/blueprints"
    },
    preferredVersions: {
      type: "object",
      properties: {
        php: {
          anyOf: [
            { $ref: "#/definitions/BlueprintPHPVersion" },
            { type: "string", const: "latest" }
          ],
          description: `The preferred PHP version to use. If not specified, the latest supported version will be used.

Note: PHP 7.2 and 7.3 are deprecated and will be automatically upgraded to 7.4.`
        },
        wp: {
          type: "string",
          description: "The preferred WordPress version to use. If not specified, the latest supported version will be used"
        }
      },
      required: ["php", "wp"],
      additionalProperties: !1,
      description: "The preferred PHP and WordPress versions to use."
    },
    features: {
      type: "object",
      properties: {
        intl: {
          type: "boolean",
          description: "Should boot with support for Intl dynamic extension"
        },
        networking: {
          type: "boolean",
          description: "Should boot with support for network request via wp_safe_remote_get?"
        }
      },
      additionalProperties: !1
    },
    extraLibraries: {
      type: "array",
      items: { $ref: "#/definitions/ExtraLibrary" },
      description: "Extra libraries to preload into the Playground instance."
    },
    constants: {
      $ref: "#/definitions/PHPConstants",
      description: "PHP Constants to define on every request"
    },
    plugins: {
      type: "array",
      items: {
        anyOf: [
          { type: "string" },
          { $ref: "#/definitions/FileReference" }
        ]
      },
      description: "WordPress plugins to install and activate"
    },
    siteOptions: {
      type: "object",
      additionalProperties: { type: "string" },
      properties: {
        blogname: { type: "string", description: "The site title" }
      },
      description: "WordPress site options to define"
    },
    login: {
      anyOf: [
        { type: "boolean" },
        {
          type: "object",
          properties: {
            username: { type: "string" },
            password: { type: "string" }
          },
          required: ["username", "password"],
          additionalProperties: !1
        }
      ],
      description: "User to log in as. If true, logs the user in as admin/password."
    },
    phpExtensionBundles: {
      deprecated: "No longer used. Feel free to remove it from your Blueprint."
    },
    steps: {
      type: "array",
      items: {
        anyOf: [
          { $ref: "#/definitions/StepDefinition" },
          { type: "string" },
          { not: {} },
          { type: "boolean", const: !1 },
          { type: "null" }
        ]
      },
      description: "The steps to run after every other operation in this Blueprint was executed."
    },
    $schema: { type: "string" }
  }
}, gs = {
  additionalProperties: { type: ["string", "boolean", "number"] }
}, Mr = Object.prototype.hasOwnProperty, bs = {
  enum: ["8.5", "8.4", "8.3", "8.2", "8.1", "8.0", "7.4"]
};
function Ie(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  const p = e;
  let d = !1;
  const y = e;
  if (typeof r != "string") {
    const h = {
      instancePath: t,
      schemaPath: "#/definitions/SupportedPHPVersion/type",
      keyword: "type",
      params: { type: "string" },
      message: "must be string"
    };
    s === null ? s = [h] : s.push(h), e++;
  }
  if (!(r === "8.5" || r === "8.4" || r === "8.3" || r === "8.2" || r === "8.1" || r === "8.0" || r === "7.4")) {
    const h = {
      instancePath: t,
      schemaPath: "#/definitions/SupportedPHPVersion/enum",
      keyword: "enum",
      params: { allowedValues: bs.enum },
      message: "must be equal to one of the allowed values"
    };
    s === null ? s = [h] : s.push(h), e++;
  }
  var _ = y === e;
  if (d = d || _, !d) {
    const h = e;
    if (typeof r != "string") {
      const E = {
        instancePath: t,
        schemaPath: "#/anyOf/1/type",
        keyword: "type",
        params: { type: "string" },
        message: "must be string"
      };
      s === null ? s = [E] : s.push(E), e++;
    }
    if (r !== "7.2") {
      const E = {
        instancePath: t,
        schemaPath: "#/anyOf/1/const",
        keyword: "const",
        params: { allowedValue: "7.2" },
        message: "must be equal to constant"
      };
      s === null ? s = [E] : s.push(E), e++;
    }
    var _ = h === e;
    if (d = d || _, !d) {
      const E = e;
      if (typeof r != "string") {
        const g = {
          instancePath: t,
          schemaPath: "#/anyOf/2/type",
          keyword: "type",
          params: { type: "string" },
          message: "must be string"
        };
        s === null ? s = [g] : s.push(g), e++;
      }
      if (r !== "7.3") {
        const g = {
          instancePath: t,
          schemaPath: "#/anyOf/2/const",
          keyword: "const",
          params: { allowedValue: "7.3" },
          message: "must be equal to constant"
        };
        s === null ? s = [g] : s.push(g), e++;
      }
      var _ = E === e;
      d = d || _;
    }
  }
  if (d)
    e = p, s !== null && (p ? s.length = p : s = null);
  else {
    const h = {
      instancePath: t,
      schemaPath: "#/anyOf",
      keyword: "anyOf",
      params: {},
      message: "must match a schema in anyOf"
    };
    return s === null ? s = [h] : s.push(h), e++, Ie.errors = s, !1;
  }
  return Ie.errors = s, e === 0;
}
const ur = { validate: Y }, ws = {
  enum: ["branch", "tag", "commit", "refname"]
};
function de(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  if (r && typeof r == "object" && !Array.isArray(r)) {
    let d;
    if (r.resource === void 0 && (d = "resource") || r.url === void 0 && (d = "url") || r.ref === void 0 && (d = "ref"))
      return de.errors = [
        {
          instancePath: t,
          schemaPath: "#/required",
          keyword: "required",
          params: { missingProperty: d },
          message: "must have required property '" + d + "'"
        }
      ], !1;
    for (const y in r)
      if (!(y === "resource" || y === "url" || y === "ref" || y === "refType" || y === "path" || y === ".git"))
        return de.errors = [
          {
            instancePath: t,
            schemaPath: "#/additionalProperties",
            keyword: "additionalProperties",
            params: { additionalProperty: y },
            message: "must NOT have additional properties"
          }
        ], !1;
    {
      if (r.resource !== void 0) {
        let y = r.resource;
        const _ = e;
        if (typeof y != "string")
          return de.errors = [
            {
              instancePath: t + "/resource",
              schemaPath: "#/properties/resource/type",
              keyword: "type",
              params: { type: "string" },
              message: "must be string"
            }
          ], !1;
        if (y !== "git:directory")
          return de.errors = [
            {
              instancePath: t + "/resource",
              schemaPath: "#/properties/resource/const",
              keyword: "const",
              params: { allowedValue: "git:directory" },
              message: "must be equal to constant"
            }
          ], !1;
        var p = _ === e;
      } else
        var p = !0;
      if (p) {
        if (r.url !== void 0) {
          const y = e;
          if (typeof r.url != "string")
            return de.errors = [
              {
                instancePath: t + "/url",
                schemaPath: "#/properties/url/type",
                keyword: "type",
                params: { type: "string" },
                message: "must be string"
              }
            ], !1;
          var p = y === e;
        } else
          var p = !0;
        if (p) {
          if (r.ref !== void 0) {
            const y = e;
            if (typeof r.ref != "string")
              return de.errors = [
                {
                  instancePath: t + "/ref",
                  schemaPath: "#/properties/ref/type",
                  keyword: "type",
                  params: { type: "string" },
                  message: "must be string"
                }
              ], !1;
            var p = y === e;
          } else
            var p = !0;
          if (p) {
            if (r.refType !== void 0) {
              let y = r.refType;
              const _ = e;
              if (typeof y != "string")
                return de.errors = [
                  {
                    instancePath: t + "/refType",
                    schemaPath: "#/definitions/GitDirectoryRefType/type",
                    keyword: "type",
                    params: { type: "string" },
                    message: "must be string"
                  }
                ], !1;
              if (!(y === "branch" || y === "tag" || y === "commit" || y === "refname"))
                return de.errors = [
                  {
                    instancePath: t + "/refType",
                    schemaPath: "#/definitions/GitDirectoryRefType/enum",
                    keyword: "enum",
                    params: {
                      allowedValues: ws.enum
                    },
                    message: "must be equal to one of the allowed values"
                  }
                ], !1;
              var p = _ === e;
            } else
              var p = !0;
            if (p) {
              if (r.path !== void 0) {
                const y = e;
                if (typeof r.path != "string")
                  return de.errors = [
                    {
                      instancePath: t + "/path",
                      schemaPath: "#/properties/path/type",
                      keyword: "type",
                      params: { type: "string" },
                      message: "must be string"
                    }
                  ], !1;
                var p = y === e;
              } else
                var p = !0;
              if (p)
                if (r[".git"] !== void 0) {
                  const y = e;
                  if (typeof r[".git"] != "boolean")
                    return de.errors = [
                      {
                        instancePath: t + "/.git",
                        schemaPath: "#/properties/.git/type",
                        keyword: "type",
                        params: {
                          type: "boolean"
                        },
                        message: "must be boolean"
                      }
                    ], !1;
                  var p = y === e;
                } else
                  var p = !0;
            }
          }
        }
      }
    }
  } else
    return de.errors = [
      {
        instancePath: t,
        schemaPath: "#/type",
        keyword: "type",
        params: { type: "object" },
        message: "must be object"
      }
    ], !1;
  return de.errors = s, e === 0;
}
const vs = {
  additionalProperties: {
    anyOf: [
      { $ref: "#/definitions/FileTree" },
      { type: ["object", "string"] }
    ]
  }
}, dr = { validate: xe };
function xe(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  if (e === 0)
    if (r && typeof r == "object" && !Array.isArray(r))
      for (const y in r) {
        let _ = r[y];
        const h = e, k = e;
        let E = !1;
        const $ = e;
        dr.validate(_, {
          instancePath: t + "/" + y.replace(/~/g, "~0").replace(/\//g, "~1"),
          parentData: r,
          parentDataProperty: y,
          rootData: u
        }) || (s = s === null ? dr.validate.errors : s.concat(dr.validate.errors), e = s.length);
        var p = $ === e;
        if (E = E || p, !E) {
          const g = e;
          if (!(_ && typeof _ == "object" && !Array.isArray(_)) && typeof _ != "string") {
            const X = {
              instancePath: t + "/" + y.replace(/~/g, "~0").replace(/\//g, "~1"),
              schemaPath: "#/additionalProperties/anyOf/1/type",
              keyword: "type",
              params: {
                type: vs.additionalProperties.anyOf[1].type
              },
              message: "must be object,string"
            };
            s === null ? s = [X] : s.push(X), e++;
          }
          var p = g === e;
          E = E || p;
        }
        if (E)
          e = k, s !== null && (k ? s.length = k : s = null);
        else {
          const g = {
            instancePath: t + "/" + y.replace(/~/g, "~0").replace(/\//g, "~1"),
            schemaPath: "#/additionalProperties/anyOf",
            keyword: "anyOf",
            params: {},
            message: "must match a schema in anyOf"
          };
          return s === null ? s = [g] : s.push(g), e++, xe.errors = s, !1;
        }
        var d = h === e;
        if (!d)
          break;
      }
    else
      return xe.errors = [
        {
          instancePath: t,
          schemaPath: "#/type",
          keyword: "type",
          params: { type: "object" },
          message: "must be object"
        }
      ], !1;
  return xe.errors = s, e === 0;
}
function $e(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  if (e === 0)
    if (r && typeof r == "object" && !Array.isArray(r)) {
      let d;
      if (r.files === void 0 && (d = "files") || r.name === void 0 && (d = "name") || r.resource === void 0 && (d = "resource"))
        return $e.errors = [
          {
            instancePath: t,
            schemaPath: "#/required",
            keyword: "required",
            params: { missingProperty: d },
            message: "must have required property '" + d + "'"
          }
        ], !1;
      {
        const y = e;
        for (const _ in r)
          if (!(_ === "resource" || _ === "files" || _ === "name"))
            return $e.errors = [
              {
                instancePath: t,
                schemaPath: "#/additionalProperties",
                keyword: "additionalProperties",
                params: { additionalProperty: _ },
                message: "must NOT have additional properties"
              }
            ], !1;
        if (y === e) {
          if (r.resource !== void 0) {
            let _ = r.resource;
            const h = e;
            if (typeof _ != "string")
              return $e.errors = [
                {
                  instancePath: t + "/resource",
                  schemaPath: "#/properties/resource/type",
                  keyword: "type",
                  params: { type: "string" },
                  message: "must be string"
                }
              ], !1;
            if (_ !== "literal:directory")
              return $e.errors = [
                {
                  instancePath: t + "/resource",
                  schemaPath: "#/properties/resource/const",
                  keyword: "const",
                  params: {
                    allowedValue: "literal:directory"
                  },
                  message: "must be equal to constant"
                }
              ], !1;
            var p = h === e;
          } else
            var p = !0;
          if (p) {
            if (r.files !== void 0) {
              const _ = e;
              xe(r.files, {
                instancePath: t + "/files",
                parentData: r,
                parentDataProperty: "files",
                rootData: u
              }) || (s = s === null ? xe.errors : s.concat(xe.errors), e = s.length);
              var p = _ === e;
            } else
              var p = !0;
            if (p)
              if (r.name !== void 0) {
                const _ = e;
                if (typeof r.name != "string")
                  return $e.errors = [
                    {
                      instancePath: t + "/name",
                      schemaPath: "#/properties/name/type",
                      keyword: "type",
                      params: { type: "string" },
                      message: "must be string"
                    }
                  ], !1;
                var p = _ === e;
              } else
                var p = !0;
          }
        }
      }
    } else
      return $e.errors = [
        {
          instancePath: t,
          schemaPath: "#/type",
          keyword: "type",
          params: { type: "object" },
          message: "must be object"
        }
      ], !1;
  return $e.errors = s, e === 0;
}
function ce(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  const p = e;
  let d = !1;
  const y = e;
  de(r, {
    instancePath: t,
    parentData: n,
    parentDataProperty: l,
    rootData: u
  }) || (s = s === null ? de.errors : s.concat(de.errors), e = s.length);
  var _ = y === e;
  if (d = d || _, !d) {
    const h = e;
    $e(r, {
      instancePath: t,
      parentData: n,
      parentDataProperty: l,
      rootData: u
    }) || (s = s === null ? $e.errors : s.concat($e.errors), e = s.length);
    var _ = h === e;
    d = d || _;
  }
  if (d)
    e = p, s !== null && (p ? s.length = p : s = null);
  else {
    const h = {
      instancePath: t,
      schemaPath: "#/anyOf",
      keyword: "anyOf",
      params: {},
      message: "must match a schema in anyOf"
    };
    return s === null ? s = [h] : s.push(h), e++, ce.errors = s, !1;
  }
  return ce.errors = s, e === 0;
}
function ve(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  if (e === 0)
    if (r && typeof r == "object" && !Array.isArray(r)) {
      let y;
      if (r.resource === void 0 && (y = "resource") || r.inner === void 0 && (y = "inner"))
        return ve.errors = [
          {
            instancePath: t,
            schemaPath: "#/required",
            keyword: "required",
            params: { missingProperty: y },
            message: "must have required property '" + y + "'"
          }
        ], !1;
      {
        const _ = e;
        for (const h in r)
          if (!(h === "resource" || h === "inner" || h === "name"))
            return ve.errors = [
              {
                instancePath: t,
                schemaPath: "#/additionalProperties",
                keyword: "additionalProperties",
                params: { additionalProperty: h },
                message: "must NOT have additional properties"
              }
            ], !1;
        if (_ === e) {
          if (r.resource !== void 0) {
            let h = r.resource;
            const k = e;
            if (typeof h != "string")
              return ve.errors = [
                {
                  instancePath: t + "/resource",
                  schemaPath: "#/properties/resource/type",
                  keyword: "type",
                  params: { type: "string" },
                  message: "must be string"
                }
              ], !1;
            if (h !== "zip")
              return ve.errors = [
                {
                  instancePath: t + "/resource",
                  schemaPath: "#/properties/resource/const",
                  keyword: "const",
                  params: { allowedValue: "zip" },
                  message: "must be equal to constant"
                }
              ], !1;
            var p = k === e;
          } else
            var p = !0;
          if (p) {
            if (r.inner !== void 0) {
              let h = r.inner;
              const k = e, E = e;
              let $ = !1;
              const g = e;
              ur.validate(h, {
                instancePath: t + "/inner",
                parentData: r,
                parentDataProperty: "inner",
                rootData: u
              }) || (s = s === null ? ur.validate.errors : s.concat(
                ur.validate.errors
              ), e = s.length);
              var d = g === e;
              if ($ = $ || d, !$) {
                const X = e;
                ce(h, {
                  instancePath: t + "/inner",
                  parentData: r,
                  parentDataProperty: "inner",
                  rootData: u
                }) || (s = s === null ? ce.errors : s.concat(ce.errors), e = s.length);
                var d = X === e;
                $ = $ || d;
              }
              if ($)
                e = E, s !== null && (E ? s.length = E : s = null);
              else {
                const X = {
                  instancePath: t + "/inner",
                  schemaPath: "#/properties/inner/anyOf",
                  keyword: "anyOf",
                  params: {},
                  message: "must match a schema in anyOf"
                };
                return s === null ? s = [X] : s.push(X), e++, ve.errors = s, !1;
              }
              var p = k === e;
            } else
              var p = !0;
            if (p)
              if (r.name !== void 0) {
                const h = e;
                if (typeof r.name != "string")
                  return ve.errors = [
                    {
                      instancePath: t + "/name",
                      schemaPath: "#/properties/name/type",
                      keyword: "type",
                      params: { type: "string" },
                      message: "must be string"
                    }
                  ], !1;
                var p = h === e;
              } else
                var p = !0;
          }
        }
      }
    } else
      return ve.errors = [
        {
          instancePath: t,
          schemaPath: "#/type",
          keyword: "type",
          params: { type: "object" },
          message: "must be object"
        }
      ], !1;
  return ve.errors = s, e === 0;
}
function Y(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  const p = e;
  let d = !1;
  const y = e;
  if (e === e)
    if (r && typeof r == "object" && !Array.isArray(r)) {
      let W;
      if (r.resource === void 0 && (W = "resource") || r.path === void 0 && (W = "path")) {
        const re = {
          instancePath: t,
          schemaPath: "#/definitions/VFSReference/required",
          keyword: "required",
          params: { missingProperty: W },
          message: "must have required property '" + W + "'"
        };
        s === null ? s = [re] : s.push(re), e++;
      } else {
        const re = e;
        for (const m in r)
          if (!(m === "resource" || m === "path")) {
            const N = {
              instancePath: t,
              schemaPath: "#/definitions/VFSReference/additionalProperties",
              keyword: "additionalProperties",
              params: { additionalProperty: m },
              message: "must NOT have additional properties"
            };
            s === null ? s = [N] : s.push(N), e++;
            break;
          }
        if (re === e) {
          if (r.resource !== void 0) {
            let m = r.resource;
            const N = e;
            if (typeof m != "string") {
              const v = {
                instancePath: t + "/resource",
                schemaPath: "#/definitions/VFSReference/properties/resource/type",
                keyword: "type",
                params: { type: "string" },
                message: "must be string"
              };
              s === null ? s = [v] : s.push(v), e++;
            }
            if (m !== "vfs") {
              const v = {
                instancePath: t + "/resource",
                schemaPath: "#/definitions/VFSReference/properties/resource/const",
                keyword: "const",
                params: { allowedValue: "vfs" },
                message: "must be equal to constant"
              };
              s === null ? s = [v] : s.push(v), e++;
            }
            var h = N === e;
          } else
            var h = !0;
          if (h)
            if (r.path !== void 0) {
              const m = e;
              if (typeof r.path != "string") {
                const v = {
                  instancePath: t + "/path",
                  schemaPath: "#/definitions/VFSReference/properties/path/type",
                  keyword: "type",
                  params: { type: "string" },
                  message: "must be string"
                };
                s === null ? s = [v] : s.push(v), e++;
              }
              var h = m === e;
            } else
              var h = !0;
        }
      }
    } else {
      const W = {
        instancePath: t,
        schemaPath: "#/definitions/VFSReference/type",
        keyword: "type",
        params: { type: "object" },
        message: "must be object"
      };
      s === null ? s = [W] : s.push(W), e++;
    }
  var k = y === e;
  if (d = d || k, !d) {
    const W = e;
    if (e === e)
      if (r && typeof r == "object" && !Array.isArray(r)) {
        let N;
        if (r.resource === void 0 && (N = "resource") || r.name === void 0 && (N = "name") || r.contents === void 0 && (N = "contents")) {
          const v = {
            instancePath: t,
            schemaPath: "#/definitions/LiteralReference/required",
            keyword: "required",
            params: { missingProperty: N },
            message: "must have required property '" + N + "'"
          };
          s === null ? s = [v] : s.push(v), e++;
        } else {
          const v = e;
          for (const b in r)
            if (!(b === "resource" || b === "name" || b === "contents")) {
              const O = {
                instancePath: t,
                schemaPath: "#/definitions/LiteralReference/additionalProperties",
                keyword: "additionalProperties",
                params: { additionalProperty: b },
                message: "must NOT have additional properties"
              };
              s === null ? s = [O] : s.push(O), e++;
              break;
            }
          if (v === e) {
            if (r.resource !== void 0) {
              let b = r.resource;
              const O = e;
              if (typeof b != "string") {
                const T = {
                  instancePath: t + "/resource",
                  schemaPath: "#/definitions/LiteralReference/properties/resource/type",
                  keyword: "type",
                  params: { type: "string" },
                  message: "must be string"
                };
                s === null ? s = [T] : s.push(T), e++;
              }
              if (b !== "literal") {
                const T = {
                  instancePath: t + "/resource",
                  schemaPath: "#/definitions/LiteralReference/properties/resource/const",
                  keyword: "const",
                  params: { allowedValue: "literal" },
                  message: "must be equal to constant"
                };
                s === null ? s = [T] : s.push(T), e++;
              }
              var E = O === e;
            } else
              var E = !0;
            if (E) {
              if (r.name !== void 0) {
                const b = e;
                if (typeof r.name != "string") {
                  const T = {
                    instancePath: t + "/name",
                    schemaPath: "#/definitions/LiteralReference/properties/name/type",
                    keyword: "type",
                    params: { type: "string" },
                    message: "must be string"
                  };
                  s === null ? s = [T] : s.push(T), e++;
                }
                var E = b === e;
              } else
                var E = !0;
              if (E)
                if (r.contents !== void 0) {
                  let b = r.contents;
                  const O = e, T = e;
                  let P = !1;
                  const w = e;
                  if (typeof b != "string") {
                    const L = {
                      instancePath: t + "/contents",
                      schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/0/type",
                      keyword: "type",
                      params: { type: "string" },
                      message: "must be string"
                    };
                    s === null ? s = [L] : s.push(L), e++;
                  }
                  var $ = w === e;
                  if (P = P || $, !P) {
                    const L = e;
                    if (e === L)
                      if (b && typeof b == "object" && !Array.isArray(b)) {
                        let F;
                        if (b.BYTES_PER_ELEMENT === void 0 && (F = "BYTES_PER_ELEMENT") || b.buffer === void 0 && (F = "buffer") || b.byteLength === void 0 && (F = "byteLength") || b.byteOffset === void 0 && (F = "byteOffset") || b.length === void 0 && (F = "length")) {
                          const I = {
                            instancePath: t + "/contents",
                            schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/required",
                            keyword: "required",
                            params: {
                              missingProperty: F
                            },
                            message: "must have required property '" + F + "'"
                          };
                          s === null ? s = [I] : s.push(I), e++;
                        } else {
                          const I = e;
                          for (const C in b)
                            if (!(C === "BYTES_PER_ELEMENT" || C === "buffer" || C === "byteLength" || C === "byteOffset" || C === "length")) {
                              let D = b[C];
                              const ee = e;
                              if (!(typeof D == "number" && isFinite(
                                D
                              ))) {
                                const S = {
                                  instancePath: t + "/contents/" + C.replace(
                                    /~/g,
                                    "~0"
                                  ).replace(
                                    /\//g,
                                    "~1"
                                  ),
                                  schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/additionalProperties/type",
                                  keyword: "type",
                                  params: {
                                    type: "number"
                                  },
                                  message: "must be number"
                                };
                                s === null ? s = [
                                  S
                                ] : s.push(
                                  S
                                ), e++;
                              }
                              var g = ee === e;
                              if (!g)
                                break;
                            }
                          if (I === e) {
                            if (b.BYTES_PER_ELEMENT !== void 0) {
                              let C = b.BYTES_PER_ELEMENT;
                              const D = e;
                              if (!(typeof C == "number" && isFinite(
                                C
                              ))) {
                                const ee = {
                                  instancePath: t + "/contents/BYTES_PER_ELEMENT",
                                  schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/properties/BYTES_PER_ELEMENT/type",
                                  keyword: "type",
                                  params: {
                                    type: "number"
                                  },
                                  message: "must be number"
                                };
                                s === null ? s = [
                                  ee
                                ] : s.push(
                                  ee
                                ), e++;
                              }
                              var B = D === e;
                            } else
                              var B = !0;
                            if (B) {
                              if (b.buffer !== void 0) {
                                let C = b.buffer;
                                const D = e;
                                if (e === D)
                                  if (C && typeof C == "object" && !Array.isArray(
                                    C
                                  )) {
                                    let S;
                                    if (C.byteLength === void 0 && (S = "byteLength")) {
                                      const H = {
                                        instancePath: t + "/contents/buffer",
                                        schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/properties/buffer/required",
                                        keyword: "required",
                                        params: {
                                          missingProperty: S
                                        },
                                        message: "must have required property '" + S + "'"
                                      };
                                      s === null ? s = [
                                        H
                                      ] : s.push(
                                        H
                                      ), e++;
                                    } else {
                                      const H = e;
                                      for (const M in C)
                                        if (M !== "byteLength") {
                                          const z = {
                                            instancePath: t + "/contents/buffer",
                                            schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/properties/buffer/additionalProperties",
                                            keyword: "additionalProperties",
                                            params: {
                                              additionalProperty: M
                                            },
                                            message: "must NOT have additional properties"
                                          };
                                          s === null ? s = [
                                            z
                                          ] : s.push(
                                            z
                                          ), e++;
                                          break;
                                        }
                                      if (H === e && C.byteLength !== void 0) {
                                        let M = C.byteLength;
                                        if (!(typeof M == "number" && isFinite(
                                          M
                                        ))) {
                                          const z = {
                                            instancePath: t + "/contents/buffer/byteLength",
                                            schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/properties/buffer/properties/byteLength/type",
                                            keyword: "type",
                                            params: {
                                              type: "number"
                                            },
                                            message: "must be number"
                                          };
                                          s === null ? s = [
                                            z
                                          ] : s.push(
                                            z
                                          ), e++;
                                        }
                                      }
                                    }
                                  } else {
                                    const S = {
                                      instancePath: t + "/contents/buffer",
                                      schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/properties/buffer/type",
                                      keyword: "type",
                                      params: {
                                        type: "object"
                                      },
                                      message: "must be object"
                                    };
                                    s === null ? s = [
                                      S
                                    ] : s.push(
                                      S
                                    ), e++;
                                  }
                                var B = D === e;
                              } else
                                var B = !0;
                              if (B) {
                                if (b.byteLength !== void 0) {
                                  let C = b.byteLength;
                                  const D = e;
                                  if (!(typeof C == "number" && isFinite(
                                    C
                                  ))) {
                                    const S = {
                                      instancePath: t + "/contents/byteLength",
                                      schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/properties/byteLength/type",
                                      keyword: "type",
                                      params: {
                                        type: "number"
                                      },
                                      message: "must be number"
                                    };
                                    s === null ? s = [
                                      S
                                    ] : s.push(
                                      S
                                    ), e++;
                                  }
                                  var B = D === e;
                                } else
                                  var B = !0;
                                if (B) {
                                  if (b.byteOffset !== void 0) {
                                    let C = b.byteOffset;
                                    const D = e;
                                    if (!(typeof C == "number" && isFinite(
                                      C
                                    ))) {
                                      const S = {
                                        instancePath: t + "/contents/byteOffset",
                                        schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/properties/byteOffset/type",
                                        keyword: "type",
                                        params: {
                                          type: "number"
                                        },
                                        message: "must be number"
                                      };
                                      s === null ? s = [
                                        S
                                      ] : s.push(
                                        S
                                      ), e++;
                                    }
                                    var B = D === e;
                                  } else
                                    var B = !0;
                                  if (B)
                                    if (b.length !== void 0) {
                                      let C = b.length;
                                      const D = e;
                                      if (!(typeof C == "number" && isFinite(
                                        C
                                      ))) {
                                        const S = {
                                          instancePath: t + "/contents/length",
                                          schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/properties/length/type",
                                          keyword: "type",
                                          params: {
                                            type: "number"
                                          },
                                          message: "must be number"
                                        };
                                        s === null ? s = [
                                          S
                                        ] : s.push(
                                          S
                                        ), e++;
                                      }
                                      var B = D === e;
                                    } else
                                      var B = !0;
                                }
                              }
                            }
                          }
                        }
                      } else {
                        const F = {
                          instancePath: t + "/contents",
                          schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf/1/type",
                          keyword: "type",
                          params: { type: "object" },
                          message: "must be object"
                        };
                        s === null ? s = [F] : s.push(F), e++;
                      }
                    var $ = L === e;
                    P = P || $;
                  }
                  if (P)
                    e = T, s !== null && (T ? s.length = T : s = null);
                  else {
                    const L = {
                      instancePath: t + "/contents",
                      schemaPath: "#/definitions/LiteralReference/properties/contents/anyOf",
                      keyword: "anyOf",
                      params: {},
                      message: "must match a schema in anyOf"
                    };
                    s === null ? s = [L] : s.push(L), e++;
                  }
                  var E = O === e;
                } else
                  var E = !0;
            }
          }
        }
      } else {
        const N = {
          instancePath: t,
          schemaPath: "#/definitions/LiteralReference/type",
          keyword: "type",
          params: { type: "object" },
          message: "must be object"
        };
        s === null ? s = [N] : s.push(N), e++;
      }
    var k = W === e;
    if (d = d || k, !d) {
      const N = e;
      if (e === e)
        if (r && typeof r == "object" && !Array.isArray(r)) {
          let O;
          if (r.resource === void 0 && (O = "resource") || r.slug === void 0 && (O = "slug")) {
            const T = {
              instancePath: t,
              schemaPath: "#/definitions/CoreThemeReference/required",
              keyword: "required",
              params: { missingProperty: O },
              message: "must have required property '" + O + "'"
            };
            s === null ? s = [T] : s.push(T), e++;
          } else {
            const T = e;
            for (const P in r)
              if (!(P === "resource" || P === "slug")) {
                const w = {
                  instancePath: t,
                  schemaPath: "#/definitions/CoreThemeReference/additionalProperties",
                  keyword: "additionalProperties",
                  params: { additionalProperty: P },
                  message: "must NOT have additional properties"
                };
                s === null ? s = [w] : s.push(w), e++;
                break;
              }
            if (T === e) {
              if (r.resource !== void 0) {
                let P = r.resource;
                const w = e;
                if (typeof P != "string") {
                  const R = {
                    instancePath: t + "/resource",
                    schemaPath: "#/definitions/CoreThemeReference/properties/resource/type",
                    keyword: "type",
                    params: { type: "string" },
                    message: "must be string"
                  };
                  s === null ? s = [R] : s.push(R), e++;
                }
                if (P !== "wordpress.org/themes") {
                  const R = {
                    instancePath: t + "/resource",
                    schemaPath: "#/definitions/CoreThemeReference/properties/resource/const",
                    keyword: "const",
                    params: {
                      allowedValue: "wordpress.org/themes"
                    },
                    message: "must be equal to constant"
                  };
                  s === null ? s = [R] : s.push(R), e++;
                }
                var X = w === e;
              } else
                var X = !0;
              if (X)
                if (r.slug !== void 0) {
                  const P = e;
                  if (typeof r.slug != "string") {
                    const R = {
                      instancePath: t + "/slug",
                      schemaPath: "#/definitions/CoreThemeReference/properties/slug/type",
                      keyword: "type",
                      params: { type: "string" },
                      message: "must be string"
                    };
                    s === null ? s = [R] : s.push(R), e++;
                  }
                  var X = P === e;
                } else
                  var X = !0;
            }
          }
        } else {
          const O = {
            instancePath: t,
            schemaPath: "#/definitions/CoreThemeReference/type",
            keyword: "type",
            params: { type: "object" },
            message: "must be object"
          };
          s === null ? s = [O] : s.push(O), e++;
        }
      var k = N === e;
      if (d = d || k, !d) {
        const O = e;
        if (e === e)
          if (r && typeof r == "object" && !Array.isArray(r)) {
            let w;
            if (r.resource === void 0 && (w = "resource") || r.slug === void 0 && (w = "slug")) {
              const R = {
                instancePath: t,
                schemaPath: "#/definitions/CorePluginReference/required",
                keyword: "required",
                params: { missingProperty: w },
                message: "must have required property '" + w + "'"
              };
              s === null ? s = [R] : s.push(R), e++;
            } else {
              const R = e;
              for (const L in r)
                if (!(L === "resource" || L === "slug")) {
                  const U = {
                    instancePath: t,
                    schemaPath: "#/definitions/CorePluginReference/additionalProperties",
                    keyword: "additionalProperties",
                    params: { additionalProperty: L },
                    message: "must NOT have additional properties"
                  };
                  s === null ? s = [U] : s.push(U), e++;
                  break;
                }
              if (R === e) {
                if (r.resource !== void 0) {
                  let L = r.resource;
                  const U = e;
                  if (typeof L != "string") {
                    const F = {
                      instancePath: t + "/resource",
                      schemaPath: "#/definitions/CorePluginReference/properties/resource/type",
                      keyword: "type",
                      params: { type: "string" },
                      message: "must be string"
                    };
                    s === null ? s = [F] : s.push(F), e++;
                  }
                  if (L !== "wordpress.org/plugins") {
                    const F = {
                      instancePath: t + "/resource",
                      schemaPath: "#/definitions/CorePluginReference/properties/resource/const",
                      keyword: "const",
                      params: {
                        allowedValue: "wordpress.org/plugins"
                      },
                      message: "must be equal to constant"
                    };
                    s === null ? s = [F] : s.push(F), e++;
                  }
                  var Z = U === e;
                } else
                  var Z = !0;
                if (Z)
                  if (r.slug !== void 0) {
                    const L = e;
                    if (typeof r.slug != "string") {
                      const F = {
                        instancePath: t + "/slug",
                        schemaPath: "#/definitions/CorePluginReference/properties/slug/type",
                        keyword: "type",
                        params: { type: "string" },
                        message: "must be string"
                      };
                      s === null ? s = [F] : s.push(F), e++;
                    }
                    var Z = L === e;
                  } else
                    var Z = !0;
              }
            }
          } else {
            const w = {
              instancePath: t,
              schemaPath: "#/definitions/CorePluginReference/type",
              keyword: "type",
              params: { type: "object" },
              message: "must be object"
            };
            s === null ? s = [w] : s.push(w), e++;
          }
        var k = O === e;
        if (d = d || k, !d) {
          const w = e;
          if (e === e)
            if (r && typeof r == "object" && !Array.isArray(r)) {
              let U;
              if (r.resource === void 0 && (U = "resource") || r.url === void 0 && (U = "url")) {
                const F = {
                  instancePath: t,
                  schemaPath: "#/definitions/UrlReference/required",
                  keyword: "required",
                  params: { missingProperty: U },
                  message: "must have required property '" + U + "'"
                };
                s === null ? s = [F] : s.push(F), e++;
              } else {
                const F = e;
                for (const I in r)
                  if (!(I === "resource" || I === "url" || I === "caption")) {
                    const C = {
                      instancePath: t,
                      schemaPath: "#/definitions/UrlReference/additionalProperties",
                      keyword: "additionalProperties",
                      params: {
                        additionalProperty: I
                      },
                      message: "must NOT have additional properties"
                    };
                    s === null ? s = [C] : s.push(C), e++;
                    break;
                  }
                if (F === e) {
                  if (r.resource !== void 0) {
                    let I = r.resource;
                    const C = e;
                    if (typeof I != "string") {
                      const D = {
                        instancePath: t + "/resource",
                        schemaPath: "#/definitions/UrlReference/properties/resource/type",
                        keyword: "type",
                        params: { type: "string" },
                        message: "must be string"
                      };
                      s === null ? s = [D] : s.push(D), e++;
                    }
                    if (I !== "url") {
                      const D = {
                        instancePath: t + "/resource",
                        schemaPath: "#/definitions/UrlReference/properties/resource/const",
                        keyword: "const",
                        params: { allowedValue: "url" },
                        message: "must be equal to constant"
                      };
                      s === null ? s = [D] : s.push(D), e++;
                    }
                    var q = C === e;
                  } else
                    var q = !0;
                  if (q) {
                    if (r.url !== void 0) {
                      const I = e;
                      if (typeof r.url != "string") {
                        const D = {
                          instancePath: t + "/url",
                          schemaPath: "#/definitions/UrlReference/properties/url/type",
                          keyword: "type",
                          params: { type: "string" },
                          message: "must be string"
                        };
                        s === null ? s = [D] : s.push(D), e++;
                      }
                      var q = I === e;
                    } else
                      var q = !0;
                    if (q)
                      if (r.caption !== void 0) {
                        const I = e;
                        if (typeof r.caption != "string") {
                          const D = {
                            instancePath: t + "/caption",
                            schemaPath: "#/definitions/UrlReference/properties/caption/type",
                            keyword: "type",
                            params: {
                              type: "string"
                            },
                            message: "must be string"
                          };
                          s === null ? s = [D] : s.push(D), e++;
                        }
                        var q = I === e;
                      } else
                        var q = !0;
                  }
                }
              }
            } else {
              const U = {
                instancePath: t,
                schemaPath: "#/definitions/UrlReference/type",
                keyword: "type",
                params: { type: "object" },
                message: "must be object"
              };
              s === null ? s = [U] : s.push(U), e++;
            }
          var k = w === e;
          if (d = d || k, !d) {
            const U = e;
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let C;
                if (r.resource === void 0 && (C = "resource") || r.path === void 0 && (C = "path")) {
                  const D = {
                    instancePath: t,
                    schemaPath: "#/definitions/BundledReference/required",
                    keyword: "required",
                    params: { missingProperty: C },
                    message: "must have required property '" + C + "'"
                  };
                  s === null ? s = [D] : s.push(D), e++;
                } else {
                  const D = e;
                  for (const ee in r)
                    if (!(ee === "resource" || ee === "path")) {
                      const S = {
                        instancePath: t,
                        schemaPath: "#/definitions/BundledReference/additionalProperties",
                        keyword: "additionalProperties",
                        params: {
                          additionalProperty: ee
                        },
                        message: "must NOT have additional properties"
                      };
                      s === null ? s = [S] : s.push(S), e++;
                      break;
                    }
                  if (D === e) {
                    if (r.resource !== void 0) {
                      let ee = r.resource;
                      const S = e;
                      if (typeof ee != "string") {
                        const H = {
                          instancePath: t + "/resource",
                          schemaPath: "#/definitions/BundledReference/properties/resource/type",
                          keyword: "type",
                          params: { type: "string" },
                          message: "must be string"
                        };
                        s === null ? s = [H] : s.push(H), e++;
                      }
                      if (ee !== "bundled") {
                        const H = {
                          instancePath: t + "/resource",
                          schemaPath: "#/definitions/BundledReference/properties/resource/const",
                          keyword: "const",
                          params: {
                            allowedValue: "bundled"
                          },
                          message: "must be equal to constant"
                        };
                        s === null ? s = [H] : s.push(H), e++;
                      }
                      var K = S === e;
                    } else
                      var K = !0;
                    if (K)
                      if (r.path !== void 0) {
                        const ee = e;
                        if (typeof r.path != "string") {
                          const H = {
                            instancePath: t + "/path",
                            schemaPath: "#/definitions/BundledReference/properties/path/type",
                            keyword: "type",
                            params: {
                              type: "string"
                            },
                            message: "must be string"
                          };
                          s === null ? s = [H] : s.push(H), e++;
                        }
                        var K = ee === e;
                      } else
                        var K = !0;
                  }
                }
              } else {
                const C = {
                  instancePath: t,
                  schemaPath: "#/definitions/BundledReference/type",
                  keyword: "type",
                  params: { type: "object" },
                  message: "must be object"
                };
                s === null ? s = [C] : s.push(C), e++;
              }
            var k = U === e;
            if (d = d || k, !d) {
              const C = e;
              ve(r, {
                instancePath: t,
                parentData: n,
                parentDataProperty: l,
                rootData: u
              }) || (s = s === null ? ve.errors : s.concat(ve.errors), e = s.length);
              var k = C === e;
              d = d || k;
            }
          }
        }
      }
    }
  }
  if (d)
    e = p, s !== null && (p ? s.length = p : s = null);
  else {
    const W = {
      instancePath: t,
      schemaPath: "#/anyOf",
      keyword: "anyOf",
      params: {},
      message: "must match a schema in anyOf"
    };
    return s === null ? s = [W] : s.push(W), e++, Y.errors = s, !1;
  }
  return Y.errors = s, e === 0;
}
const tr = {
  oneOf: [
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "activatePlugin" },
        pluginPath: {
          type: "string",
          description: "Path to the plugin directory as absolute path (/wordpress/wp-content/plugins/plugin-name); or the plugin entry file relative to the plugins directory (plugin-name/plugin-name.php)."
        },
        pluginName: {
          type: "string",
          description: "Optional. Plugin name to display in the progress bar."
        }
      },
      required: ["pluginPath", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "activateTheme" },
        themeFolderName: {
          type: "string",
          description: "The name of the theme folder inside wp-content/themes/"
        }
      },
      required: ["step", "themeFolderName"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "cp" },
        fromPath: { type: "string", description: "Source path" },
        toPath: { type: "string", description: "Target path" }
      },
      required: ["fromPath", "step", "toPath"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "defineWpConfigConsts" },
        consts: {
          type: "object",
          additionalProperties: {},
          description: "The constants to define"
        },
        method: {
          type: "string",
          enum: ["rewrite-wp-config", "define-before-run"],
          description: `The method of defining the constants in wp-config.php. Possible values are:

- rewrite-wp-config: Default. Rewrites the wp-config.php file to                      explicitly call define() with the requested                      name and value. This method alters the file                      on the disk, but it doesn't conflict with                      existing define() calls in wp-config.php.

- define-before-run: Defines the constant before running the requested                      script. It doesn't alter any files on the disk, but                      constants defined this way may conflict with existing                      define() calls in wp-config.php.`
        },
        virtualize: {
          type: "boolean",
          deprecated: `This option is noop and will be removed in a future version.
This option is only kept in here to avoid breaking Blueprint schema validation
for existing apps using this option.`
        }
      },
      required: ["consts", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "defineSiteUrl" },
        siteUrl: { type: "string", description: "The URL" }
      },
      required: ["siteUrl", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "enableMultisite" },
        wpCliPath: { type: "string", description: "wp-cli.phar path" }
      },
      required: ["step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "importWxr" },
        file: {
          $ref: "#/definitions/FileReference",
          description: "The file to import"
        },
        importer: {
          type: "string",
          enum: ["data-liberation", "default"],
          description: "The importer to use. Possible values:\n\n- `default`: The importer from https://github.com/humanmade/WordPress-Importer\n- `data-liberation`: The experimental Data Liberation WXR importer developed at                      https://github.com/WordPress/wordpress-playground/issues/1894\n\nThis option is deprecated. The syntax will not be removed, but once the Data Liberation importer matures, it will become the only supported importer and the `importer` option will be ignored.",
          deprecated: !0
        }
      },
      required: ["file", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: {
          type: "string",
          const: "importThemeStarterContent",
          description: "The step identifier."
        },
        themeSlug: {
          type: "string",
          description: "The name of the theme to import content from."
        }
      },
      required: ["step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "importWordPressFiles" },
        wordPressFilesZip: {
          $ref: "#/definitions/FileReference",
          description: "The zip file containing the top-level WordPress files and directories."
        },
        pathInZip: {
          type: "string",
          description: "The path inside the zip file where the WordPress files are."
        }
      },
      required: ["step", "wordPressFilesZip"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        ifAlreadyInstalled: {
          type: "string",
          enum: ["overwrite", "skip", "error"],
          description: "What to do if the asset already exists."
        },
        step: {
          type: "string",
          const: "installPlugin",
          description: "The step identifier."
        },
        pluginData: {
          anyOf: [
            { $ref: "#/definitions/FileReference" },
            { $ref: "#/definitions/DirectoryReference" }
          ],
          description: "The plugin files to install. It can be a plugin zip file, a single PHP file, or a directory containing all the plugin files at its root."
        },
        pluginZipFile: {
          $ref: "#/definitions/FileReference",
          deprecated: ". Use 'pluginData' instead."
        },
        options: {
          $ref: "#/definitions/InstallPluginOptions",
          description: "Optional installation options."
        }
      },
      required: ["pluginData", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        ifAlreadyInstalled: {
          type: "string",
          enum: ["overwrite", "skip", "error"],
          description: "What to do if the asset already exists."
        },
        step: {
          type: "string",
          const: "installTheme",
          description: "The step identifier."
        },
        themeData: {
          anyOf: [
            { $ref: "#/definitions/FileReference" },
            { $ref: "#/definitions/DirectoryReference" }
          ],
          description: "The theme files to install. It can be either a theme zip file, or a directory containing all the theme files at its root."
        },
        themeZipFile: {
          $ref: "#/definitions/FileReference",
          deprecated: ". Use 'themeData' instead."
        },
        options: {
          $ref: "#/definitions/InstallThemeOptions",
          description: "Optional installation options."
        }
      },
      required: ["step", "themeData"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "login" },
        username: {
          type: "string",
          description: "The user to log in as. Defaults to 'admin'."
        },
        password: {
          type: "string",
          deprecated: `The password field is deprecated and will be removed in a future version.
Only the username field is required for user authentication.`
        }
      },
      required: ["step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "mkdir" },
        path: {
          type: "string",
          description: "The path of the directory you want to create"
        }
      },
      required: ["path", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "mv" },
        fromPath: { type: "string", description: "Source path" },
        toPath: { type: "string", description: "Target path" }
      },
      required: ["fromPath", "step", "toPath"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "resetData" }
      },
      required: ["step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "request" },
        request: {
          $ref: "#/definitions/PHPRequest",
          description: "Request details (See /wordpress-playground/api/universal/interface/PHPRequest)"
        }
      },
      required: ["request", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "rm" },
        path: { type: "string", description: "The path to remove" }
      },
      required: ["path", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "rmdir" },
        path: { type: "string", description: "The path to remove" }
      },
      required: ["path", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: {
          type: "string",
          const: "runPHP",
          description: "The step identifier."
        },
        code: {
          anyOf: [
            { type: "string" },
            {
              type: "object",
              properties: {
                filename: {
                  type: "string",
                  description: "This property is ignored during Blueprint v1 execution but exists so the same runPHP step structure can be used for Blueprints v1 and v2."
                },
                content: { type: "string" }
              },
              required: ["filename", "content"],
              additionalProperties: !1
            }
          ],
          description: "The PHP code to run."
        }
      },
      required: ["code", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "runPHPWithOptions" },
        options: {
          $ref: "#/definitions/PHPRunOptions",
          description: "Run options (See /wordpress-playground/api/universal/interface/PHPRunOptions/))"
        }
      },
      required: ["options", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "runWpInstallationWizard" },
        options: { $ref: "#/definitions/WordPressInstallationOptions" }
      },
      required: ["options", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: {
          type: "string",
          const: "runSql",
          description: "The step identifier."
        },
        sql: {
          $ref: "#/definitions/FileReference",
          description: "The SQL to run. Each non-empty line must contain a valid SQL query."
        }
      },
      required: ["sql", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: {
          type: "string",
          const: "setSiteOptions",
          description: 'The name of the step. Must be "setSiteOptions".'
        },
        options: {
          type: "object",
          additionalProperties: {},
          description: "The options to set on the site."
        }
      },
      required: ["options", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "unzip" },
        zipFile: {
          $ref: "#/definitions/FileReference",
          description: "The zip file to extract"
        },
        zipPath: {
          type: "string",
          description: "The path of the zip file to extract",
          deprecated: "Use zipFile instead."
        },
        extractToPath: {
          type: "string",
          description: "The path to extract the zip file to"
        }
      },
      required: ["extractToPath", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "updateUserMeta" },
        meta: {
          type: "object",
          additionalProperties: {},
          description: 'An object of user meta values to set, e.g. { "first_name": "John" }'
        },
        userId: { type: "number", description: "User ID" }
      },
      required: ["meta", "step", "userId"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "writeFile" },
        path: {
          type: "string",
          description: "The path of the file to write to"
        },
        data: {
          anyOf: [
            { $ref: "#/definitions/FileReference" },
            { type: "string" },
            {
              type: "object",
              properties: {
                BYTES_PER_ELEMENT: { type: "number" },
                buffer: {
                  type: "object",
                  properties: {
                    byteLength: { type: "number" }
                  },
                  required: ["byteLength"],
                  additionalProperties: !1
                },
                byteLength: { type: "number" },
                byteOffset: { type: "number" },
                length: { type: "number" }
              },
              required: [
                "BYTES_PER_ELEMENT",
                "buffer",
                "byteLength",
                "byteOffset",
                "length"
              ],
              additionalProperties: { type: "number" }
            }
          ],
          description: "The data to write"
        }
      },
      required: ["data", "path", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "writeFiles" },
        writeToPath: {
          type: "string",
          description: "The path of the file to write to"
        },
        filesTree: {
          $ref: "#/definitions/DirectoryReference",
          description: "The 'filesTree' defines the directory structure, supporting 'literal:directory' or 'git:directory' types. The 'name' represents the root directory, while 'files' is an object where keys are file paths, and values contain either file content as a string or nested objects for subdirectories."
        }
      },
      required: ["filesTree", "step", "writeToPath"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: {
          type: "string",
          const: "wp-cli",
          description: "The step identifier."
        },
        command: {
          anyOf: [
            { type: "string" },
            { type: "array", items: { type: "string" } }
          ],
          description: "The WP CLI command to run."
        },
        wpCliPath: { type: "string", description: "wp-cli.phar path" }
      },
      required: ["command", "step"]
    },
    {
      type: "object",
      additionalProperties: !1,
      properties: {
        progress: {
          type: "object",
          properties: {
            weight: { type: "number" },
            caption: { type: "string" }
          },
          additionalProperties: !1
        },
        step: { type: "string", const: "setSiteLanguage" },
        language: {
          type: "string",
          description: "The language to set, e.g. 'en_US'"
        }
      },
      required: ["language", "step"]
    }
  ]
}, zr = {
  enum: ["GET", "POST", "HEAD", "OPTIONS", "PATCH", "PUT", "DELETE"]
};
function ge(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  if (e === 0)
    if (r && typeof r == "object" && !Array.isArray(r)) {
      let X;
      if (r.url === void 0 && (X = "url"))
        return ge.errors = [
          {
            instancePath: t,
            schemaPath: "#/required",
            keyword: "required",
            params: { missingProperty: X },
            message: "must have required property '" + X + "'"
          }
        ], !1;
      {
        const Z = e;
        for (const q in r)
          if (!(q === "method" || q === "url" || q === "headers" || q === "body"))
            return ge.errors = [
              {
                instancePath: t,
                schemaPath: "#/additionalProperties",
                keyword: "additionalProperties",
                params: { additionalProperty: q },
                message: "must NOT have additional properties"
              }
            ], !1;
        if (Z === e) {
          if (r.method !== void 0) {
            let q = r.method;
            const K = e;
            if (typeof q != "string")
              return ge.errors = [
                {
                  instancePath: t + "/method",
                  schemaPath: "#/definitions/HTTPMethod/type",
                  keyword: "type",
                  params: { type: "string" },
                  message: "must be string"
                }
              ], !1;
            if (!(q === "GET" || q === "POST" || q === "HEAD" || q === "OPTIONS" || q === "PATCH" || q === "PUT" || q === "DELETE"))
              return ge.errors = [
                {
                  instancePath: t + "/method",
                  schemaPath: "#/definitions/HTTPMethod/enum",
                  keyword: "enum",
                  params: { allowedValues: zr.enum },
                  message: "must be equal to one of the allowed values"
                }
              ], !1;
            var p = K === e;
          } else
            var p = !0;
          if (p) {
            if (r.url !== void 0) {
              const q = e;
              if (typeof r.url != "string")
                return ge.errors = [
                  {
                    instancePath: t + "/url",
                    schemaPath: "#/properties/url/type",
                    keyword: "type",
                    params: { type: "string" },
                    message: "must be string"
                  }
                ], !1;
              var p = q === e;
            } else
              var p = !0;
            if (p) {
              if (r.headers !== void 0) {
                let q = r.headers;
                const K = e;
                if (e === e)
                  if (q && typeof q == "object" && !Array.isArray(q))
                    for (const m in q) {
                      const N = e;
                      if (typeof q[m] != "string")
                        return ge.errors = [
                          {
                            instancePath: t + "/headers/" + m.replace(
                              /~/g,
                              "~0"
                            ).replace(
                              /\//g,
                              "~1"
                            ),
                            schemaPath: "#/definitions/PHPRequestHeaders/additionalProperties/type",
                            keyword: "type",
                            params: {
                              type: "string"
                            },
                            message: "must be string"
                          }
                        ], !1;
                      var d = N === e;
                      if (!d)
                        break;
                    }
                  else
                    return ge.errors = [
                      {
                        instancePath: t + "/headers",
                        schemaPath: "#/definitions/PHPRequestHeaders/type",
                        keyword: "type",
                        params: { type: "object" },
                        message: "must be object"
                      }
                    ], !1;
                var p = K === e;
              } else
                var p = !0;
              if (p)
                if (r.body !== void 0) {
                  let q = r.body;
                  const K = e, W = e;
                  let re = !1;
                  const m = e;
                  if (typeof q != "string") {
                    const v = {
                      instancePath: t + "/body",
                      schemaPath: "#/properties/body/anyOf/0/type",
                      keyword: "type",
                      params: { type: "string" },
                      message: "must be string"
                    };
                    s === null ? s = [v] : s.push(v), e++;
                  }
                  var y = m === e;
                  if (re = re || y, !re) {
                    const v = e;
                    if (e === v)
                      if (q && typeof q == "object" && !Array.isArray(q)) {
                        let O;
                        if (q.BYTES_PER_ELEMENT === void 0 && (O = "BYTES_PER_ELEMENT") || q.buffer === void 0 && (O = "buffer") || q.byteLength === void 0 && (O = "byteLength") || q.byteOffset === void 0 && (O = "byteOffset") || q.length === void 0 && (O = "length")) {
                          const T = {
                            instancePath: t + "/body",
                            schemaPath: "#/properties/body/anyOf/1/required",
                            keyword: "required",
                            params: {
                              missingProperty: O
                            },
                            message: "must have required property '" + O + "'"
                          };
                          s === null ? s = [T] : s.push(T), e++;
                        } else {
                          const T = e;
                          for (const P in q)
                            if (!(P === "BYTES_PER_ELEMENT" || P === "buffer" || P === "byteLength" || P === "byteOffset" || P === "length")) {
                              let w = q[P];
                              const R = e;
                              if (!(typeof w == "number" && isFinite(
                                w
                              ))) {
                                const L = {
                                  instancePath: t + "/body/" + P.replace(
                                    /~/g,
                                    "~0"
                                  ).replace(
                                    /\//g,
                                    "~1"
                                  ),
                                  schemaPath: "#/properties/body/anyOf/1/additionalProperties/type",
                                  keyword: "type",
                                  params: {
                                    type: "number"
                                  },
                                  message: "must be number"
                                };
                                s === null ? s = [
                                  L
                                ] : s.push(
                                  L
                                ), e++;
                              }
                              var _ = R === e;
                              if (!_)
                                break;
                            }
                          if (T === e) {
                            if (q.BYTES_PER_ELEMENT !== void 0) {
                              let P = q.BYTES_PER_ELEMENT;
                              const w = e;
                              if (!(typeof P == "number" && isFinite(
                                P
                              ))) {
                                const R = {
                                  instancePath: t + "/body/BYTES_PER_ELEMENT",
                                  schemaPath: "#/properties/body/anyOf/1/properties/BYTES_PER_ELEMENT/type",
                                  keyword: "type",
                                  params: {
                                    type: "number"
                                  },
                                  message: "must be number"
                                };
                                s === null ? s = [
                                  R
                                ] : s.push(
                                  R
                                ), e++;
                              }
                              var h = w === e;
                            } else
                              var h = !0;
                            if (h) {
                              if (q.buffer !== void 0) {
                                let P = q.buffer;
                                const w = e;
                                if (e === w)
                                  if (P && typeof P == "object" && !Array.isArray(
                                    P
                                  )) {
                                    let L;
                                    if (P.byteLength === void 0 && (L = "byteLength")) {
                                      const U = {
                                        instancePath: t + "/body/buffer",
                                        schemaPath: "#/properties/body/anyOf/1/properties/buffer/required",
                                        keyword: "required",
                                        params: {
                                          missingProperty: L
                                        },
                                        message: "must have required property '" + L + "'"
                                      };
                                      s === null ? s = [
                                        U
                                      ] : s.push(
                                        U
                                      ), e++;
                                    } else {
                                      const U = e;
                                      for (const F in P)
                                        if (F !== "byteLength") {
                                          const I = {
                                            instancePath: t + "/body/buffer",
                                            schemaPath: "#/properties/body/anyOf/1/properties/buffer/additionalProperties",
                                            keyword: "additionalProperties",
                                            params: {
                                              additionalProperty: F
                                            },
                                            message: "must NOT have additional properties"
                                          };
                                          s === null ? s = [
                                            I
                                          ] : s.push(
                                            I
                                          ), e++;
                                          break;
                                        }
                                      if (U === e && P.byteLength !== void 0) {
                                        let F = P.byteLength;
                                        if (!(typeof F == "number" && isFinite(
                                          F
                                        ))) {
                                          const I = {
                                            instancePath: t + "/body/buffer/byteLength",
                                            schemaPath: "#/properties/body/anyOf/1/properties/buffer/properties/byteLength/type",
                                            keyword: "type",
                                            params: {
                                              type: "number"
                                            },
                                            message: "must be number"
                                          };
                                          s === null ? s = [
                                            I
                                          ] : s.push(
                                            I
                                          ), e++;
                                        }
                                      }
                                    }
                                  } else {
                                    const L = {
                                      instancePath: t + "/body/buffer",
                                      schemaPath: "#/properties/body/anyOf/1/properties/buffer/type",
                                      keyword: "type",
                                      params: {
                                        type: "object"
                                      },
                                      message: "must be object"
                                    };
                                    s === null ? s = [
                                      L
                                    ] : s.push(
                                      L
                                    ), e++;
                                  }
                                var h = w === e;
                              } else
                                var h = !0;
                              if (h) {
                                if (q.byteLength !== void 0) {
                                  let P = q.byteLength;
                                  const w = e;
                                  if (!(typeof P == "number" && isFinite(
                                    P
                                  ))) {
                                    const L = {
                                      instancePath: t + "/body/byteLength",
                                      schemaPath: "#/properties/body/anyOf/1/properties/byteLength/type",
                                      keyword: "type",
                                      params: {
                                        type: "number"
                                      },
                                      message: "must be number"
                                    };
                                    s === null ? s = [
                                      L
                                    ] : s.push(
                                      L
                                    ), e++;
                                  }
                                  var h = w === e;
                                } else
                                  var h = !0;
                                if (h) {
                                  if (q.byteOffset !== void 0) {
                                    let P = q.byteOffset;
                                    const w = e;
                                    if (!(typeof P == "number" && isFinite(
                                      P
                                    ))) {
                                      const L = {
                                        instancePath: t + "/body/byteOffset",
                                        schemaPath: "#/properties/body/anyOf/1/properties/byteOffset/type",
                                        keyword: "type",
                                        params: {
                                          type: "number"
                                        },
                                        message: "must be number"
                                      };
                                      s === null ? s = [
                                        L
                                      ] : s.push(
                                        L
                                      ), e++;
                                    }
                                    var h = w === e;
                                  } else
                                    var h = !0;
                                  if (h)
                                    if (q.length !== void 0) {
                                      let P = q.length;
                                      const w = e;
                                      if (!(typeof P == "number" && isFinite(
                                        P
                                      ))) {
                                        const L = {
                                          instancePath: t + "/body/length",
                                          schemaPath: "#/properties/body/anyOf/1/properties/length/type",
                                          keyword: "type",
                                          params: {
                                            type: "number"
                                          },
                                          message: "must be number"
                                        };
                                        s === null ? s = [
                                          L
                                        ] : s.push(
                                          L
                                        ), e++;
                                      }
                                      var h = w === e;
                                    } else
                                      var h = !0;
                                }
                              }
                            }
                          }
                        }
                      } else {
                        const O = {
                          instancePath: t + "/body",
                          schemaPath: "#/properties/body/anyOf/1/type",
                          keyword: "type",
                          params: { type: "object" },
                          message: "must be object"
                        };
                        s === null ? s = [O] : s.push(O), e++;
                      }
                    var y = v === e;
                    if (re = re || y, !re) {
                      const O = e;
                      if (e === O)
                        if (q && typeof q == "object" && !Array.isArray(q))
                          for (const P in q) {
                            let w = q[P];
                            const R = e, L = e;
                            let U = !1;
                            const F = e;
                            if (typeof w != "string") {
                              const I = {
                                instancePath: t + "/body/" + P.replace(
                                  /~/g,
                                  "~0"
                                ).replace(
                                  /\//g,
                                  "~1"
                                ),
                                schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/0/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              };
                              s === null ? s = [
                                I
                              ] : s.push(
                                I
                              ), e++;
                            }
                            var k = F === e;
                            if (U = U || k, !U) {
                              const I = e;
                              if (e === I)
                                if (w && typeof w == "object" && !Array.isArray(
                                  w
                                )) {
                                  let D;
                                  if (w.BYTES_PER_ELEMENT === void 0 && (D = "BYTES_PER_ELEMENT") || w.buffer === void 0 && (D = "buffer") || w.byteLength === void 0 && (D = "byteLength") || w.byteOffset === void 0 && (D = "byteOffset") || w.length === void 0 && (D = "length")) {
                                    const ee = {
                                      instancePath: t + "/body/" + P.replace(
                                        /~/g,
                                        "~0"
                                      ).replace(
                                        /\//g,
                                        "~1"
                                      ),
                                      schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/required",
                                      keyword: "required",
                                      params: {
                                        missingProperty: D
                                      },
                                      message: "must have required property '" + D + "'"
                                    };
                                    s === null ? s = [
                                      ee
                                    ] : s.push(
                                      ee
                                    ), e++;
                                  } else {
                                    const ee = e;
                                    for (const S in w)
                                      if (!(S === "BYTES_PER_ELEMENT" || S === "buffer" || S === "byteLength" || S === "byteOffset" || S === "length")) {
                                        let H = w[S];
                                        const M = e;
                                        if (!(typeof H == "number" && isFinite(
                                          H
                                        ))) {
                                          const z = {
                                            instancePath: t + "/body/" + P.replace(
                                              /~/g,
                                              "~0"
                                            ).replace(
                                              /\//g,
                                              "~1"
                                            ) + "/" + S.replace(
                                              /~/g,
                                              "~0"
                                            ).replace(
                                              /\//g,
                                              "~1"
                                            ),
                                            schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/additionalProperties/type",
                                            keyword: "type",
                                            params: {
                                              type: "number"
                                            },
                                            message: "must be number"
                                          };
                                          s === null ? s = [
                                            z
                                          ] : s.push(
                                            z
                                          ), e++;
                                        }
                                        var E = M === e;
                                        if (!E)
                                          break;
                                      }
                                    if (ee === e) {
                                      if (w.BYTES_PER_ELEMENT !== void 0) {
                                        let S = w.BYTES_PER_ELEMENT;
                                        const H = e;
                                        if (!(typeof S == "number" && isFinite(
                                          S
                                        ))) {
                                          const M = {
                                            instancePath: t + "/body/" + P.replace(
                                              /~/g,
                                              "~0"
                                            ).replace(
                                              /\//g,
                                              "~1"
                                            ) + "/BYTES_PER_ELEMENT",
                                            schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/properties/BYTES_PER_ELEMENT/type",
                                            keyword: "type",
                                            params: {
                                              type: "number"
                                            },
                                            message: "must be number"
                                          };
                                          s === null ? s = [
                                            M
                                          ] : s.push(
                                            M
                                          ), e++;
                                        }
                                        var $ = H === e;
                                      } else
                                        var $ = !0;
                                      if ($) {
                                        if (w.buffer !== void 0) {
                                          let S = w.buffer;
                                          const H = e;
                                          if (e === H)
                                            if (S && typeof S == "object" && !Array.isArray(
                                              S
                                            )) {
                                              let z;
                                              if (S.byteLength === void 0 && (z = "byteLength")) {
                                                const oe = {
                                                  instancePath: t + "/body/" + P.replace(
                                                    /~/g,
                                                    "~0"
                                                  ).replace(
                                                    /\//g,
                                                    "~1"
                                                  ) + "/buffer",
                                                  schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/properties/buffer/required",
                                                  keyword: "required",
                                                  params: {
                                                    missingProperty: z
                                                  },
                                                  message: "must have required property '" + z + "'"
                                                };
                                                s === null ? s = [
                                                  oe
                                                ] : s.push(
                                                  oe
                                                ), e++;
                                              } else {
                                                const oe = e;
                                                for (const ue in S)
                                                  if (ue !== "byteLength") {
                                                    const Pe = {
                                                      instancePath: t + "/body/" + P.replace(
                                                        /~/g,
                                                        "~0"
                                                      ).replace(
                                                        /\//g,
                                                        "~1"
                                                      ) + "/buffer",
                                                      schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/properties/buffer/additionalProperties",
                                                      keyword: "additionalProperties",
                                                      params: {
                                                        additionalProperty: ue
                                                      },
                                                      message: "must NOT have additional properties"
                                                    };
                                                    s === null ? s = [
                                                      Pe
                                                    ] : s.push(
                                                      Pe
                                                    ), e++;
                                                    break;
                                                  }
                                                if (oe === e && S.byteLength !== void 0) {
                                                  let ue = S.byteLength;
                                                  if (!(typeof ue == "number" && isFinite(
                                                    ue
                                                  ))) {
                                                    const Pe = {
                                                      instancePath: t + "/body/" + P.replace(
                                                        /~/g,
                                                        "~0"
                                                      ).replace(
                                                        /\//g,
                                                        "~1"
                                                      ) + "/buffer/byteLength",
                                                      schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/properties/buffer/properties/byteLength/type",
                                                      keyword: "type",
                                                      params: {
                                                        type: "number"
                                                      },
                                                      message: "must be number"
                                                    };
                                                    s === null ? s = [
                                                      Pe
                                                    ] : s.push(
                                                      Pe
                                                    ), e++;
                                                  }
                                                }
                                              }
                                            } else {
                                              const z = {
                                                instancePath: t + "/body/" + P.replace(
                                                  /~/g,
                                                  "~0"
                                                ).replace(
                                                  /\//g,
                                                  "~1"
                                                ) + "/buffer",
                                                schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/properties/buffer/type",
                                                keyword: "type",
                                                params: {
                                                  type: "object"
                                                },
                                                message: "must be object"
                                              };
                                              s === null ? s = [
                                                z
                                              ] : s.push(
                                                z
                                              ), e++;
                                            }
                                          var $ = H === e;
                                        } else
                                          var $ = !0;
                                        if ($) {
                                          if (w.byteLength !== void 0) {
                                            let S = w.byteLength;
                                            const H = e;
                                            if (!(typeof S == "number" && isFinite(
                                              S
                                            ))) {
                                              const z = {
                                                instancePath: t + "/body/" + P.replace(
                                                  /~/g,
                                                  "~0"
                                                ).replace(
                                                  /\//g,
                                                  "~1"
                                                ) + "/byteLength",
                                                schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/properties/byteLength/type",
                                                keyword: "type",
                                                params: {
                                                  type: "number"
                                                },
                                                message: "must be number"
                                              };
                                              s === null ? s = [
                                                z
                                              ] : s.push(
                                                z
                                              ), e++;
                                            }
                                            var $ = H === e;
                                          } else
                                            var $ = !0;
                                          if ($) {
                                            if (w.byteOffset !== void 0) {
                                              let S = w.byteOffset;
                                              const H = e;
                                              if (!(typeof S == "number" && isFinite(
                                                S
                                              ))) {
                                                const z = {
                                                  instancePath: t + "/body/" + P.replace(
                                                    /~/g,
                                                    "~0"
                                                  ).replace(
                                                    /\//g,
                                                    "~1"
                                                  ) + "/byteOffset",
                                                  schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/properties/byteOffset/type",
                                                  keyword: "type",
                                                  params: {
                                                    type: "number"
                                                  },
                                                  message: "must be number"
                                                };
                                                s === null ? s = [
                                                  z
                                                ] : s.push(
                                                  z
                                                ), e++;
                                              }
                                              var $ = H === e;
                                            } else
                                              var $ = !0;
                                            if ($)
                                              if (w.length !== void 0) {
                                                let S = w.length;
                                                const H = e;
                                                if (!(typeof S == "number" && isFinite(
                                                  S
                                                ))) {
                                                  const z = {
                                                    instancePath: t + "/body/" + P.replace(
                                                      /~/g,
                                                      "~0"
                                                    ).replace(
                                                      /\//g,
                                                      "~1"
                                                    ) + "/length",
                                                    schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/properties/length/type",
                                                    keyword: "type",
                                                    params: {
                                                      type: "number"
                                                    },
                                                    message: "must be number"
                                                  };
                                                  s === null ? s = [
                                                    z
                                                  ] : s.push(
                                                    z
                                                  ), e++;
                                                }
                                                var $ = H === e;
                                              } else
                                                var $ = !0;
                                          }
                                        }
                                      }
                                    }
                                  }
                                } else {
                                  const D = {
                                    instancePath: t + "/body/" + P.replace(
                                      /~/g,
                                      "~0"
                                    ).replace(
                                      /\//g,
                                      "~1"
                                    ),
                                    schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/1/type",
                                    keyword: "type",
                                    params: {
                                      type: "object"
                                    },
                                    message: "must be object"
                                  };
                                  s === null ? s = [
                                    D
                                  ] : s.push(
                                    D
                                  ), e++;
                                }
                              var k = I === e;
                              if (U = U || k, !U) {
                                const D = e;
                                if (e === D)
                                  if (w && typeof w == "object" && !Array.isArray(
                                    w
                                  )) {
                                    let S;
                                    if (w.lastModified === void 0 && (S = "lastModified") || w.name === void 0 && (S = "name") || w.size === void 0 && (S = "size") || w.type === void 0 && (S = "type") || w.webkitRelativePath === void 0 && (S = "webkitRelativePath")) {
                                      const H = {
                                        instancePath: t + "/body/" + P.replace(
                                          /~/g,
                                          "~0"
                                        ).replace(
                                          /\//g,
                                          "~1"
                                        ),
                                        schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/2/required",
                                        keyword: "required",
                                        params: {
                                          missingProperty: S
                                        },
                                        message: "must have required property '" + S + "'"
                                      };
                                      s === null ? s = [
                                        H
                                      ] : s.push(
                                        H
                                      ), e++;
                                    } else {
                                      const H = e;
                                      for (const M in w)
                                        if (!(M === "size" || M === "type" || M === "lastModified" || M === "name" || M === "webkitRelativePath")) {
                                          const z = {
                                            instancePath: t + "/body/" + P.replace(
                                              /~/g,
                                              "~0"
                                            ).replace(
                                              /\//g,
                                              "~1"
                                            ),
                                            schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/2/additionalProperties",
                                            keyword: "additionalProperties",
                                            params: {
                                              additionalProperty: M
                                            },
                                            message: "must NOT have additional properties"
                                          };
                                          s === null ? s = [
                                            z
                                          ] : s.push(
                                            z
                                          ), e++;
                                          break;
                                        }
                                      if (H === e) {
                                        if (w.size !== void 0) {
                                          let M = w.size;
                                          const z = e;
                                          if (!(typeof M == "number" && isFinite(
                                            M
                                          ))) {
                                            const oe = {
                                              instancePath: t + "/body/" + P.replace(
                                                /~/g,
                                                "~0"
                                              ).replace(
                                                /\//g,
                                                "~1"
                                              ) + "/size",
                                              schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/2/properties/size/type",
                                              keyword: "type",
                                              params: {
                                                type: "number"
                                              },
                                              message: "must be number"
                                            };
                                            s === null ? s = [
                                              oe
                                            ] : s.push(
                                              oe
                                            ), e++;
                                          }
                                          var g = z === e;
                                        } else
                                          var g = !0;
                                        if (g) {
                                          if (w.type !== void 0) {
                                            const M = e;
                                            if (typeof w.type != "string") {
                                              const oe = {
                                                instancePath: t + "/body/" + P.replace(
                                                  /~/g,
                                                  "~0"
                                                ).replace(
                                                  /\//g,
                                                  "~1"
                                                ) + "/type",
                                                schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/2/properties/type/type",
                                                keyword: "type",
                                                params: {
                                                  type: "string"
                                                },
                                                message: "must be string"
                                              };
                                              s === null ? s = [
                                                oe
                                              ] : s.push(
                                                oe
                                              ), e++;
                                            }
                                            var g = M === e;
                                          } else
                                            var g = !0;
                                          if (g) {
                                            if (w.lastModified !== void 0) {
                                              let M = w.lastModified;
                                              const z = e;
                                              if (!(typeof M == "number" && isFinite(
                                                M
                                              ))) {
                                                const ue = {
                                                  instancePath: t + "/body/" + P.replace(
                                                    /~/g,
                                                    "~0"
                                                  ).replace(
                                                    /\//g,
                                                    "~1"
                                                  ) + "/lastModified",
                                                  schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/2/properties/lastModified/type",
                                                  keyword: "type",
                                                  params: {
                                                    type: "number"
                                                  },
                                                  message: "must be number"
                                                };
                                                s === null ? s = [
                                                  ue
                                                ] : s.push(
                                                  ue
                                                ), e++;
                                              }
                                              var g = z === e;
                                            } else
                                              var g = !0;
                                            if (g) {
                                              if (w.name !== void 0) {
                                                const M = e;
                                                if (typeof w.name != "string") {
                                                  const oe = {
                                                    instancePath: t + "/body/" + P.replace(
                                                      /~/g,
                                                      "~0"
                                                    ).replace(
                                                      /\//g,
                                                      "~1"
                                                    ) + "/name",
                                                    schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/2/properties/name/type",
                                                    keyword: "type",
                                                    params: {
                                                      type: "string"
                                                    },
                                                    message: "must be string"
                                                  };
                                                  s === null ? s = [
                                                    oe
                                                  ] : s.push(
                                                    oe
                                                  ), e++;
                                                }
                                                var g = M === e;
                                              } else
                                                var g = !0;
                                              if (g)
                                                if (w.webkitRelativePath !== void 0) {
                                                  const M = e;
                                                  if (typeof w.webkitRelativePath != "string") {
                                                    const oe = {
                                                      instancePath: t + "/body/" + P.replace(
                                                        /~/g,
                                                        "~0"
                                                      ).replace(
                                                        /\//g,
                                                        "~1"
                                                      ) + "/webkitRelativePath",
                                                      schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/2/properties/webkitRelativePath/type",
                                                      keyword: "type",
                                                      params: {
                                                        type: "string"
                                                      },
                                                      message: "must be string"
                                                    };
                                                    s === null ? s = [
                                                      oe
                                                    ] : s.push(
                                                      oe
                                                    ), e++;
                                                  }
                                                  var g = M === e;
                                                } else
                                                  var g = !0;
                                            }
                                          }
                                        }
                                      }
                                    }
                                  } else {
                                    const S = {
                                      instancePath: t + "/body/" + P.replace(
                                        /~/g,
                                        "~0"
                                      ).replace(
                                        /\//g,
                                        "~1"
                                      ),
                                      schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf/2/type",
                                      keyword: "type",
                                      params: {
                                        type: "object"
                                      },
                                      message: "must be object"
                                    };
                                    s === null ? s = [
                                      S
                                    ] : s.push(
                                      S
                                    ), e++;
                                  }
                                var k = D === e;
                                U = U || k;
                              }
                            }
                            if (U)
                              e = L, s !== null && (L ? s.length = L : s = null);
                            else {
                              const I = {
                                instancePath: t + "/body/" + P.replace(
                                  /~/g,
                                  "~0"
                                ).replace(
                                  /\//g,
                                  "~1"
                                ),
                                schemaPath: "#/properties/body/anyOf/2/additionalProperties/anyOf",
                                keyword: "anyOf",
                                params: {},
                                message: "must match a schema in anyOf"
                              };
                              s === null ? s = [
                                I
                              ] : s.push(
                                I
                              ), e++;
                            }
                            var B = R === e;
                            if (!B)
                              break;
                          }
                        else {
                          const P = {
                            instancePath: t + "/body",
                            schemaPath: "#/properties/body/anyOf/2/type",
                            keyword: "type",
                            params: {
                              type: "object"
                            },
                            message: "must be object"
                          };
                          s === null ? s = [P] : s.push(P), e++;
                        }
                      var y = O === e;
                      re = re || y;
                    }
                  }
                  if (re)
                    e = W, s !== null && (W ? s.length = W : s = null);
                  else {
                    const v = {
                      instancePath: t + "/body",
                      schemaPath: "#/properties/body/anyOf",
                      keyword: "anyOf",
                      params: {},
                      message: "must match a schema in anyOf"
                    };
                    return s === null ? s = [v] : s.push(v), e++, ge.errors = s, !1;
                  }
                  var p = K === e;
                } else
                  var p = !0;
            }
          }
        }
      }
    } else
      return ge.errors = [
        {
          instancePath: t,
          schemaPath: "#/type",
          keyword: "type",
          params: { type: "object" },
          message: "must be object"
        }
      ], !1;
  return ge.errors = s, e === 0;
}
const Ps = {
  properties: {
    relativeUri: {
      type: "string",
      description: "Request path following the domain:port part – after any URL rewriting rules (e.g. apache .htaccess) have been applied."
    },
    scriptPath: {
      type: "string",
      description: "Path of the .php file to execute."
    },
    protocol: { type: "string", description: "Request protocol." },
    method: {
      $ref: "#/definitions/HTTPMethod",
      description: "Request method. Default: `GET`."
    },
    headers: {
      $ref: "#/definitions/PHPRequestHeaders",
      description: "Request headers."
    },
    body: {
      anyOf: [
        { type: "string" },
        {
          type: "object",
          properties: {
            BYTES_PER_ELEMENT: { type: "number" },
            buffer: {
              type: "object",
              properties: { byteLength: { type: "number" } },
              required: ["byteLength"],
              additionalProperties: !1
            },
            byteLength: { type: "number" },
            byteOffset: { type: "number" },
            length: { type: "number" }
          },
          required: [
            "BYTES_PER_ELEMENT",
            "buffer",
            "byteLength",
            "byteOffset",
            "length"
          ],
          additionalProperties: { type: "number" }
        }
      ],
      description: "Request body."
    },
    env: {
      type: "object",
      additionalProperties: { type: "string" },
      description: "Environment variables to set for this run."
    },
    $_SERVER: {
      type: "object",
      additionalProperties: { type: "string" },
      description: "$_SERVER entries to set for this run."
    },
    code: {
      type: "string",
      description: "The code snippet to eval instead of a php file."
    }
  }
};
function pe(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  if (e === 0)
    if (r && typeof r == "object" && !Array.isArray(r)) {
      const $ = e;
      for (const g in r)
        if (!Mr.call(Ps.properties, g))
          return pe.errors = [
            {
              instancePath: t,
              schemaPath: "#/additionalProperties",
              keyword: "additionalProperties",
              params: { additionalProperty: g },
              message: "must NOT have additional properties"
            }
          ], !1;
      if ($ === e) {
        if (r.relativeUri !== void 0) {
          const g = e;
          if (typeof r.relativeUri != "string")
            return pe.errors = [
              {
                instancePath: t + "/relativeUri",
                schemaPath: "#/properties/relativeUri/type",
                keyword: "type",
                params: { type: "string" },
                message: "must be string"
              }
            ], !1;
          var p = g === e;
        } else
          var p = !0;
        if (p) {
          if (r.scriptPath !== void 0) {
            const g = e;
            if (typeof r.scriptPath != "string")
              return pe.errors = [
                {
                  instancePath: t + "/scriptPath",
                  schemaPath: "#/properties/scriptPath/type",
                  keyword: "type",
                  params: { type: "string" },
                  message: "must be string"
                }
              ], !1;
            var p = g === e;
          } else
            var p = !0;
          if (p) {
            if (r.protocol !== void 0) {
              const g = e;
              if (typeof r.protocol != "string")
                return pe.errors = [
                  {
                    instancePath: t + "/protocol",
                    schemaPath: "#/properties/protocol/type",
                    keyword: "type",
                    params: { type: "string" },
                    message: "must be string"
                  }
                ], !1;
              var p = g === e;
            } else
              var p = !0;
            if (p) {
              if (r.method !== void 0) {
                let g = r.method;
                const B = e;
                if (typeof g != "string")
                  return pe.errors = [
                    {
                      instancePath: t + "/method",
                      schemaPath: "#/definitions/HTTPMethod/type",
                      keyword: "type",
                      params: { type: "string" },
                      message: "must be string"
                    }
                  ], !1;
                if (!(g === "GET" || g === "POST" || g === "HEAD" || g === "OPTIONS" || g === "PATCH" || g === "PUT" || g === "DELETE"))
                  return pe.errors = [
                    {
                      instancePath: t + "/method",
                      schemaPath: "#/definitions/HTTPMethod/enum",
                      keyword: "enum",
                      params: {
                        allowedValues: zr.enum
                      },
                      message: "must be equal to one of the allowed values"
                    }
                  ], !1;
                var p = B === e;
              } else
                var p = !0;
              if (p) {
                if (r.headers !== void 0) {
                  let g = r.headers;
                  const B = e;
                  if (e === e)
                    if (g && typeof g == "object" && !Array.isArray(g))
                      for (const q in g) {
                        const K = e;
                        if (typeof g[q] != "string")
                          return pe.errors = [
                            {
                              instancePath: t + "/headers/" + q.replace(
                                /~/g,
                                "~0"
                              ).replace(
                                /\//g,
                                "~1"
                              ),
                              schemaPath: "#/definitions/PHPRequestHeaders/additionalProperties/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        var d = K === e;
                        if (!d)
                          break;
                      }
                    else
                      return pe.errors = [
                        {
                          instancePath: t + "/headers",
                          schemaPath: "#/definitions/PHPRequestHeaders/type",
                          keyword: "type",
                          params: { type: "object" },
                          message: "must be object"
                        }
                      ], !1;
                  var p = B === e;
                } else
                  var p = !0;
                if (p) {
                  if (r.body !== void 0) {
                    let g = r.body;
                    const B = e, X = e;
                    let Z = !1;
                    const q = e;
                    if (typeof g != "string") {
                      const W = {
                        instancePath: t + "/body",
                        schemaPath: "#/properties/body/anyOf/0/type",
                        keyword: "type",
                        params: { type: "string" },
                        message: "must be string"
                      };
                      s === null ? s = [W] : s.push(W), e++;
                    }
                    var y = q === e;
                    if (Z = Z || y, !Z) {
                      const W = e;
                      if (e === W)
                        if (g && typeof g == "object" && !Array.isArray(g)) {
                          let m;
                          if (g.BYTES_PER_ELEMENT === void 0 && (m = "BYTES_PER_ELEMENT") || g.buffer === void 0 && (m = "buffer") || g.byteLength === void 0 && (m = "byteLength") || g.byteOffset === void 0 && (m = "byteOffset") || g.length === void 0 && (m = "length")) {
                            const N = {
                              instancePath: t + "/body",
                              schemaPath: "#/properties/body/anyOf/1/required",
                              keyword: "required",
                              params: {
                                missingProperty: m
                              },
                              message: "must have required property '" + m + "'"
                            };
                            s === null ? s = [N] : s.push(N), e++;
                          } else {
                            const N = e;
                            for (const v in g)
                              if (!(v === "BYTES_PER_ELEMENT" || v === "buffer" || v === "byteLength" || v === "byteOffset" || v === "length")) {
                                let b = g[v];
                                const O = e;
                                if (!(typeof b == "number" && isFinite(
                                  b
                                ))) {
                                  const T = {
                                    instancePath: t + "/body/" + v.replace(
                                      /~/g,
                                      "~0"
                                    ).replace(
                                      /\//g,
                                      "~1"
                                    ),
                                    schemaPath: "#/properties/body/anyOf/1/additionalProperties/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  };
                                  s === null ? s = [
                                    T
                                  ] : s.push(
                                    T
                                  ), e++;
                                }
                                var _ = O === e;
                                if (!_)
                                  break;
                              }
                            if (N === e) {
                              if (g.BYTES_PER_ELEMENT !== void 0) {
                                let v = g.BYTES_PER_ELEMENT;
                                const b = e;
                                if (!(typeof v == "number" && isFinite(
                                  v
                                ))) {
                                  const O = {
                                    instancePath: t + "/body/BYTES_PER_ELEMENT",
                                    schemaPath: "#/properties/body/anyOf/1/properties/BYTES_PER_ELEMENT/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  };
                                  s === null ? s = [
                                    O
                                  ] : s.push(
                                    O
                                  ), e++;
                                }
                                var h = b === e;
                              } else
                                var h = !0;
                              if (h) {
                                if (g.buffer !== void 0) {
                                  let v = g.buffer;
                                  const b = e;
                                  if (e === b)
                                    if (v && typeof v == "object" && !Array.isArray(
                                      v
                                    )) {
                                      let T;
                                      if (v.byteLength === void 0 && (T = "byteLength")) {
                                        const P = {
                                          instancePath: t + "/body/buffer",
                                          schemaPath: "#/properties/body/anyOf/1/properties/buffer/required",
                                          keyword: "required",
                                          params: {
                                            missingProperty: T
                                          },
                                          message: "must have required property '" + T + "'"
                                        };
                                        s === null ? s = [
                                          P
                                        ] : s.push(
                                          P
                                        ), e++;
                                      } else {
                                        const P = e;
                                        for (const w in v)
                                          if (w !== "byteLength") {
                                            const R = {
                                              instancePath: t + "/body/buffer",
                                              schemaPath: "#/properties/body/anyOf/1/properties/buffer/additionalProperties",
                                              keyword: "additionalProperties",
                                              params: {
                                                additionalProperty: w
                                              },
                                              message: "must NOT have additional properties"
                                            };
                                            s === null ? s = [
                                              R
                                            ] : s.push(
                                              R
                                            ), e++;
                                            break;
                                          }
                                        if (P === e && v.byteLength !== void 0) {
                                          let w = v.byteLength;
                                          if (!(typeof w == "number" && isFinite(
                                            w
                                          ))) {
                                            const R = {
                                              instancePath: t + "/body/buffer/byteLength",
                                              schemaPath: "#/properties/body/anyOf/1/properties/buffer/properties/byteLength/type",
                                              keyword: "type",
                                              params: {
                                                type: "number"
                                              },
                                              message: "must be number"
                                            };
                                            s === null ? s = [
                                              R
                                            ] : s.push(
                                              R
                                            ), e++;
                                          }
                                        }
                                      }
                                    } else {
                                      const T = {
                                        instancePath: t + "/body/buffer",
                                        schemaPath: "#/properties/body/anyOf/1/properties/buffer/type",
                                        keyword: "type",
                                        params: {
                                          type: "object"
                                        },
                                        message: "must be object"
                                      };
                                      s === null ? s = [
                                        T
                                      ] : s.push(
                                        T
                                      ), e++;
                                    }
                                  var h = b === e;
                                } else
                                  var h = !0;
                                if (h) {
                                  if (g.byteLength !== void 0) {
                                    let v = g.byteLength;
                                    const b = e;
                                    if (!(typeof v == "number" && isFinite(
                                      v
                                    ))) {
                                      const T = {
                                        instancePath: t + "/body/byteLength",
                                        schemaPath: "#/properties/body/anyOf/1/properties/byteLength/type",
                                        keyword: "type",
                                        params: {
                                          type: "number"
                                        },
                                        message: "must be number"
                                      };
                                      s === null ? s = [
                                        T
                                      ] : s.push(
                                        T
                                      ), e++;
                                    }
                                    var h = b === e;
                                  } else
                                    var h = !0;
                                  if (h) {
                                    if (g.byteOffset !== void 0) {
                                      let v = g.byteOffset;
                                      const b = e;
                                      if (!(typeof v == "number" && isFinite(
                                        v
                                      ))) {
                                        const T = {
                                          instancePath: t + "/body/byteOffset",
                                          schemaPath: "#/properties/body/anyOf/1/properties/byteOffset/type",
                                          keyword: "type",
                                          params: {
                                            type: "number"
                                          },
                                          message: "must be number"
                                        };
                                        s === null ? s = [
                                          T
                                        ] : s.push(
                                          T
                                        ), e++;
                                      }
                                      var h = b === e;
                                    } else
                                      var h = !0;
                                    if (h)
                                      if (g.length !== void 0) {
                                        let v = g.length;
                                        const b = e;
                                        if (!(typeof v == "number" && isFinite(
                                          v
                                        ))) {
                                          const T = {
                                            instancePath: t + "/body/length",
                                            schemaPath: "#/properties/body/anyOf/1/properties/length/type",
                                            keyword: "type",
                                            params: {
                                              type: "number"
                                            },
                                            message: "must be number"
                                          };
                                          s === null ? s = [
                                            T
                                          ] : s.push(
                                            T
                                          ), e++;
                                        }
                                        var h = b === e;
                                      } else
                                        var h = !0;
                                  }
                                }
                              }
                            }
                          }
                        } else {
                          const m = {
                            instancePath: t + "/body",
                            schemaPath: "#/properties/body/anyOf/1/type",
                            keyword: "type",
                            params: {
                              type: "object"
                            },
                            message: "must be object"
                          };
                          s === null ? s = [m] : s.push(m), e++;
                        }
                      var y = W === e;
                      Z = Z || y;
                    }
                    if (Z)
                      e = X, s !== null && (X ? s.length = X : s = null);
                    else {
                      const W = {
                        instancePath: t + "/body",
                        schemaPath: "#/properties/body/anyOf",
                        keyword: "anyOf",
                        params: {},
                        message: "must match a schema in anyOf"
                      };
                      return s === null ? s = [W] : s.push(W), e++, pe.errors = s, !1;
                    }
                    var p = B === e;
                  } else
                    var p = !0;
                  if (p) {
                    if (r.env !== void 0) {
                      let g = r.env;
                      const B = e;
                      if (e === B)
                        if (g && typeof g == "object" && !Array.isArray(g))
                          for (const Z in g) {
                            const q = e;
                            if (typeof g[Z] != "string")
                              return pe.errors = [
                                {
                                  instancePath: t + "/env/" + Z.replace(
                                    /~/g,
                                    "~0"
                                  ).replace(
                                    /\//g,
                                    "~1"
                                  ),
                                  schemaPath: "#/properties/env/additionalProperties/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var k = q === e;
                            if (!k)
                              break;
                          }
                        else
                          return pe.errors = [
                            {
                              instancePath: t + "/env",
                              schemaPath: "#/properties/env/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var p = B === e;
                    } else
                      var p = !0;
                    if (p) {
                      if (r.$_SERVER !== void 0) {
                        let g = r.$_SERVER;
                        const B = e;
                        if (e === B)
                          if (g && typeof g == "object" && !Array.isArray(g))
                            for (const Z in g) {
                              const q = e;
                              if (typeof g[Z] != "string")
                                return pe.errors = [
                                  {
                                    instancePath: t + "/$_SERVER/" + Z.replace(
                                      /~/g,
                                      "~0"
                                    ).replace(
                                      /\//g,
                                      "~1"
                                    ),
                                    schemaPath: "#/properties/%24_SERVER/additionalProperties/type",
                                    keyword: "type",
                                    params: {
                                      type: "string"
                                    },
                                    message: "must be string"
                                  }
                                ], !1;
                              var E = q === e;
                              if (!E)
                                break;
                            }
                          else
                            return pe.errors = [
                              {
                                instancePath: t + "/$_SERVER",
                                schemaPath: "#/properties/%24_SERVER/type",
                                keyword: "type",
                                params: {
                                  type: "object"
                                },
                                message: "must be object"
                              }
                            ], !1;
                        var p = B === e;
                      } else
                        var p = !0;
                      if (p)
                        if (r.code !== void 0) {
                          const g = e;
                          if (typeof r.code != "string")
                            return pe.errors = [
                              {
                                instancePath: t + "/code",
                                schemaPath: "#/properties/code/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var p = g === e;
                        } else
                          var p = !0;
                    }
                  }
                }
              }
            }
          }
        }
      }
    } else
      return pe.errors = [
        {
          instancePath: t,
          schemaPath: "#/type",
          keyword: "type",
          params: { type: "object" },
          message: "must be object"
        }
      ], !1;
  return pe.errors = s, e === 0;
}
function o(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  if (e === 0)
    if (r && typeof r == "object" && !Array.isArray(r)) {
      let lr;
      if (r.step === void 0 && (lr = "step"))
        return o.errors = [
          {
            instancePath: t,
            schemaPath: "#/required",
            keyword: "required",
            params: { missingProperty: lr },
            message: "must have required property '" + lr + "'"
          }
        ], !1;
      {
        const G = r.step;
        if (typeof G == "string")
          if (G === "activatePlugin") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.pluginPath === void 0 && (c = "pluginPath") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/0/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "pluginPath" || i === "pluginName"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/0/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/0/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/0/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var p = A === e;
                            } else
                              var p = !0;
                            if (p)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/0/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var p = a === e;
                              } else
                                var p = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/0/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var d = f === e;
                    } else
                      var d = !0;
                    if (d) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/0/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "activatePlugin")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/0/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "activatePlugin"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var d = f === e;
                      } else
                        var d = !0;
                      if (d) {
                        if (r.pluginPath !== void 0) {
                          const i = e;
                          if (typeof r.pluginPath != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/pluginPath",
                                schemaPath: "#/oneOf/0/properties/pluginPath/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var d = i === e;
                        } else
                          var d = !0;
                        if (d)
                          if (r.pluginName !== void 0) {
                            const i = e;
                            if (typeof r.pluginName != "string")
                              return o.errors = [
                                {
                                  instancePath: t + "/pluginName",
                                  schemaPath: "#/oneOf/0/properties/pluginName/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var d = i === e;
                          } else
                            var d = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/0/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "activateTheme") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.step === void 0 && (c = "step") || r.themeFolderName === void 0 && (c = "themeFolderName"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/1/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "themeFolderName"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/1/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/1/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/1/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var y = A === e;
                            } else
                              var y = !0;
                            if (y)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/1/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var y = a === e;
                              } else
                                var y = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/1/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var _ = f === e;
                    } else
                      var _ = !0;
                    if (_) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/1/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "activateTheme")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/1/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "activateTheme"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var _ = f === e;
                      } else
                        var _ = !0;
                      if (_)
                        if (r.themeFolderName !== void 0) {
                          const i = e;
                          if (typeof r.themeFolderName != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/themeFolderName",
                                schemaPath: "#/oneOf/1/properties/themeFolderName/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var _ = i === e;
                        } else
                          var _ = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/1/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "cp") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.fromPath === void 0 && (c = "fromPath") || r.step === void 0 && (c = "step") || r.toPath === void 0 && (c = "toPath"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/2/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "fromPath" || i === "toPath"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/2/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/2/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/2/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var h = A === e;
                            } else
                              var h = !0;
                            if (h)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/2/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var h = a === e;
                              } else
                                var h = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/2/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var k = f === e;
                    } else
                      var k = !0;
                    if (k) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/2/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "cp")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/2/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "cp"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var k = f === e;
                      } else
                        var k = !0;
                      if (k) {
                        if (r.fromPath !== void 0) {
                          const i = e;
                          if (typeof r.fromPath != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/fromPath",
                                schemaPath: "#/oneOf/2/properties/fromPath/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var k = i === e;
                        } else
                          var k = !0;
                        if (k)
                          if (r.toPath !== void 0) {
                            const i = e;
                            if (typeof r.toPath != "string")
                              return o.errors = [
                                {
                                  instancePath: t + "/toPath",
                                  schemaPath: "#/oneOf/2/properties/toPath/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var k = i === e;
                          } else
                            var k = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/2/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "defineWpConfigConsts") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.consts === void 0 && (c = "consts") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/3/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "consts" || i === "method" || i === "virtualize"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/3/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/3/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/3/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var E = A === e;
                            } else
                              var E = !0;
                            if (E)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/3/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var E = a === e;
                              } else
                                var E = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/3/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var $ = f === e;
                    } else
                      var $ = !0;
                    if ($) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/3/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "defineWpConfigConsts")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/3/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "defineWpConfigConsts"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var $ = f === e;
                      } else
                        var $ = !0;
                      if ($) {
                        if (r.consts !== void 0) {
                          let i = r.consts;
                          const f = e;
                          if (e === f && !(i && typeof i == "object" && !Array.isArray(
                            i
                          )))
                            return o.errors = [
                              {
                                instancePath: t + "/consts",
                                schemaPath: "#/oneOf/3/properties/consts/type",
                                keyword: "type",
                                params: {
                                  type: "object"
                                },
                                message: "must be object"
                              }
                            ], !1;
                          var $ = f === e;
                        } else
                          var $ = !0;
                        if ($) {
                          if (r.method !== void 0) {
                            let i = r.method;
                            const f = e;
                            if (typeof i != "string")
                              return o.errors = [
                                {
                                  instancePath: t + "/method",
                                  schemaPath: "#/oneOf/3/properties/method/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            if (!(i === "rewrite-wp-config" || i === "define-before-run"))
                              return o.errors = [
                                {
                                  instancePath: t + "/method",
                                  schemaPath: "#/oneOf/3/properties/method/enum",
                                  keyword: "enum",
                                  params: {
                                    allowedValues: tr.oneOf[3].properties.method.enum
                                  },
                                  message: "must be equal to one of the allowed values"
                                }
                              ], !1;
                            var $ = f === e;
                          } else
                            var $ = !0;
                          if ($)
                            if (r.virtualize !== void 0) {
                              const i = e;
                              if (typeof r.virtualize != "boolean")
                                return o.errors = [
                                  {
                                    instancePath: t + "/virtualize",
                                    schemaPath: "#/oneOf/3/properties/virtualize/type",
                                    keyword: "type",
                                    params: {
                                      type: "boolean"
                                    },
                                    message: "must be boolean"
                                  }
                                ], !1;
                              var $ = i === e;
                            } else
                              var $ = !0;
                        }
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/3/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "defineSiteUrl") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.siteUrl === void 0 && (c = "siteUrl") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/4/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "siteUrl"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/4/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/4/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/4/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var g = A === e;
                            } else
                              var g = !0;
                            if (g)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/4/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var g = a === e;
                              } else
                                var g = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/4/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var B = f === e;
                    } else
                      var B = !0;
                    if (B) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/4/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "defineSiteUrl")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/4/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "defineSiteUrl"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var B = f === e;
                      } else
                        var B = !0;
                      if (B)
                        if (r.siteUrl !== void 0) {
                          const i = e;
                          if (typeof r.siteUrl != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/siteUrl",
                                schemaPath: "#/oneOf/4/properties/siteUrl/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var B = i === e;
                        } else
                          var B = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/4/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "enableMultisite") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/5/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "wpCliPath"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/5/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/5/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/5/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var X = A === e;
                            } else
                              var X = !0;
                            if (X)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/5/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var X = a === e;
                              } else
                                var X = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/5/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var Z = f === e;
                    } else
                      var Z = !0;
                    if (Z) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/5/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "enableMultisite")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/5/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "enableMultisite"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var Z = f === e;
                      } else
                        var Z = !0;
                      if (Z)
                        if (r.wpCliPath !== void 0) {
                          const i = e;
                          if (typeof r.wpCliPath != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/wpCliPath",
                                schemaPath: "#/oneOf/5/properties/wpCliPath/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var Z = i === e;
                        } else
                          var Z = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/5/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "importWxr") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.file === void 0 && (c = "file") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/6/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "file" || i === "importer"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/6/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/6/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/6/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var q = A === e;
                            } else
                              var q = !0;
                            if (q)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/6/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var q = a === e;
                              } else
                                var q = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/6/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var K = f === e;
                    } else
                      var K = !0;
                    if (K) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/6/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "importWxr")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/6/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "importWxr"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var K = f === e;
                      } else
                        var K = !0;
                      if (K) {
                        if (r.file !== void 0) {
                          const i = e;
                          Y(r.file, {
                            instancePath: t + "/file",
                            parentData: r,
                            parentDataProperty: "file",
                            rootData: u
                          }) || (s = s === null ? Y.errors : s.concat(
                            Y.errors
                          ), e = s.length);
                          var K = i === e;
                        } else
                          var K = !0;
                        if (K)
                          if (r.importer !== void 0) {
                            let i = r.importer;
                            const f = e;
                            if (typeof i != "string")
                              return o.errors = [
                                {
                                  instancePath: t + "/importer",
                                  schemaPath: "#/oneOf/6/properties/importer/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            if (!(i === "data-liberation" || i === "default"))
                              return o.errors = [
                                {
                                  instancePath: t + "/importer",
                                  schemaPath: "#/oneOf/6/properties/importer/enum",
                                  keyword: "enum",
                                  params: {
                                    allowedValues: tr.oneOf[6].properties.importer.enum
                                  },
                                  message: "must be equal to one of the allowed values"
                                }
                              ], !1;
                            var K = f === e;
                          } else
                            var K = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/6/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "importThemeStarterContent") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/7/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "themeSlug"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/7/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/7/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/7/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var W = A === e;
                            } else
                              var W = !0;
                            if (W)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/7/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var W = a === e;
                              } else
                                var W = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/7/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var re = f === e;
                    } else
                      var re = !0;
                    if (re) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/7/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "importThemeStarterContent")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/7/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "importThemeStarterContent"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var re = f === e;
                      } else
                        var re = !0;
                      if (re)
                        if (r.themeSlug !== void 0) {
                          const i = e;
                          if (typeof r.themeSlug != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/themeSlug",
                                schemaPath: "#/oneOf/7/properties/themeSlug/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var re = i === e;
                        } else
                          var re = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/7/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "importWordPressFiles") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.step === void 0 && (c = "step") || r.wordPressFilesZip === void 0 && (c = "wordPressFilesZip"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/8/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "wordPressFilesZip" || i === "pathInZip"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/8/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/8/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/8/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var m = A === e;
                            } else
                              var m = !0;
                            if (m)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/8/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var m = a === e;
                              } else
                                var m = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/8/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var N = f === e;
                    } else
                      var N = !0;
                    if (N) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/8/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "importWordPressFiles")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/8/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "importWordPressFiles"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var N = f === e;
                      } else
                        var N = !0;
                      if (N) {
                        if (r.wordPressFilesZip !== void 0) {
                          const i = e;
                          Y(
                            r.wordPressFilesZip,
                            {
                              instancePath: t + "/wordPressFilesZip",
                              parentData: r,
                              parentDataProperty: "wordPressFilesZip",
                              rootData: u
                            }
                          ) || (s = s === null ? Y.errors : s.concat(
                            Y.errors
                          ), e = s.length);
                          var N = i === e;
                        } else
                          var N = !0;
                        if (N)
                          if (r.pathInZip !== void 0) {
                            const i = e;
                            if (typeof r.pathInZip != "string")
                              return o.errors = [
                                {
                                  instancePath: t + "/pathInZip",
                                  schemaPath: "#/oneOf/8/properties/pathInZip/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var N = i === e;
                          } else
                            var N = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/8/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "installPlugin") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.pluginData === void 0 && (c = "pluginData") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/9/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "ifAlreadyInstalled" || i === "step" || i === "pluginData" || i === "pluginZipFile" || i === "options"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/9/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/9/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/9/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var v = A === e;
                            } else
                              var v = !0;
                            if (v)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/9/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var v = a === e;
                              } else
                                var v = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/9/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var b = f === e;
                    } else
                      var b = !0;
                    if (b) {
                      if (r.ifAlreadyInstalled !== void 0) {
                        let i = r.ifAlreadyInstalled;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/ifAlreadyInstalled",
                              schemaPath: "#/oneOf/9/properties/ifAlreadyInstalled/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (!(i === "overwrite" || i === "skip" || i === "error"))
                          return o.errors = [
                            {
                              instancePath: t + "/ifAlreadyInstalled",
                              schemaPath: "#/oneOf/9/properties/ifAlreadyInstalled/enum",
                              keyword: "enum",
                              params: {
                                allowedValues: tr.oneOf[9].properties.ifAlreadyInstalled.enum
                              },
                              message: "must be equal to one of the allowed values"
                            }
                          ], !1;
                        var b = f === e;
                      } else
                        var b = !0;
                      if (b) {
                        if (r.step !== void 0) {
                          let i = r.step;
                          const f = e;
                          if (typeof i != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/step",
                                schemaPath: "#/oneOf/9/properties/step/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          if (i !== "installPlugin")
                            return o.errors = [
                              {
                                instancePath: t + "/step",
                                schemaPath: "#/oneOf/9/properties/step/const",
                                keyword: "const",
                                params: {
                                  allowedValue: "installPlugin"
                                },
                                message: "must be equal to constant"
                              }
                            ], !1;
                          var b = f === e;
                        } else
                          var b = !0;
                        if (b) {
                          if (r.pluginData !== void 0) {
                            let i = r.pluginData;
                            const f = e, j = e;
                            let a = !1;
                            const A = e;
                            Y(
                              i,
                              {
                                instancePath: t + "/pluginData",
                                parentData: r,
                                parentDataProperty: "pluginData",
                                rootData: u
                              }
                            ) || (s = s === null ? Y.errors : s.concat(
                              Y.errors
                            ), e = s.length);
                            var O = A === e;
                            if (a = a || O, !a) {
                              const J = e;
                              ce(
                                i,
                                {
                                  instancePath: t + "/pluginData",
                                  parentData: r,
                                  parentDataProperty: "pluginData",
                                  rootData: u
                                }
                              ) || (s = s === null ? ce.errors : s.concat(
                                ce.errors
                              ), e = s.length);
                              var O = J === e;
                              a = a || O;
                            }
                            if (a)
                              e = j, s !== null && (j ? s.length = j : s = null);
                            else {
                              const J = {
                                instancePath: t + "/pluginData",
                                schemaPath: "#/oneOf/9/properties/pluginData/anyOf",
                                keyword: "anyOf",
                                params: {},
                                message: "must match a schema in anyOf"
                              };
                              return s === null ? s = [
                                J
                              ] : s.push(
                                J
                              ), e++, o.errors = s, !1;
                            }
                            var b = f === e;
                          } else
                            var b = !0;
                          if (b) {
                            if (r.pluginZipFile !== void 0) {
                              const i = e;
                              Y(
                                r.pluginZipFile,
                                {
                                  instancePath: t + "/pluginZipFile",
                                  parentData: r,
                                  parentDataProperty: "pluginZipFile",
                                  rootData: u
                                }
                              ) || (s = s === null ? Y.errors : s.concat(
                                Y.errors
                              ), e = s.length);
                              var b = i === e;
                            } else
                              var b = !0;
                            if (b)
                              if (r.options !== void 0) {
                                let i = r.options;
                                const f = e;
                                if (e === e)
                                  if (i && typeof i == "object" && !Array.isArray(
                                    i
                                  )) {
                                    const A = e;
                                    for (const se in i)
                                      if (!(se === "activate" || se === "targetFolderName"))
                                        return o.errors = [
                                          {
                                            instancePath: t + "/options",
                                            schemaPath: "#/definitions/InstallPluginOptions/additionalProperties",
                                            keyword: "additionalProperties",
                                            params: {
                                              additionalProperty: se
                                            },
                                            message: "must NOT have additional properties"
                                          }
                                        ], !1;
                                    if (A === e) {
                                      if (i.activate !== void 0) {
                                        const se = e;
                                        if (typeof i.activate != "boolean")
                                          return o.errors = [
                                            {
                                              instancePath: t + "/options/activate",
                                              schemaPath: "#/definitions/InstallPluginOptions/properties/activate/type",
                                              keyword: "type",
                                              params: {
                                                type: "boolean"
                                              },
                                              message: "must be boolean"
                                            }
                                          ], !1;
                                        var T = se === e;
                                      } else
                                        var T = !0;
                                      if (T)
                                        if (i.targetFolderName !== void 0) {
                                          const se = e;
                                          if (typeof i.targetFolderName != "string")
                                            return o.errors = [
                                              {
                                                instancePath: t + "/options/targetFolderName",
                                                schemaPath: "#/definitions/InstallPluginOptions/properties/targetFolderName/type",
                                                keyword: "type",
                                                params: {
                                                  type: "string"
                                                },
                                                message: "must be string"
                                              }
                                            ], !1;
                                          var T = se === e;
                                        } else
                                          var T = !0;
                                    }
                                  } else
                                    return o.errors = [
                                      {
                                        instancePath: t + "/options",
                                        schemaPath: "#/definitions/InstallPluginOptions/type",
                                        keyword: "type",
                                        params: {
                                          type: "object"
                                        },
                                        message: "must be object"
                                      }
                                    ], !1;
                                var b = f === e;
                              } else
                                var b = !0;
                          }
                        }
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/9/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "installTheme") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.step === void 0 && (c = "step") || r.themeData === void 0 && (c = "themeData"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/10/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "ifAlreadyInstalled" || i === "step" || i === "themeData" || i === "themeZipFile" || i === "options"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/10/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/10/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/10/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var P = A === e;
                            } else
                              var P = !0;
                            if (P)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/10/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var P = a === e;
                              } else
                                var P = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/10/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var w = f === e;
                    } else
                      var w = !0;
                    if (w) {
                      if (r.ifAlreadyInstalled !== void 0) {
                        let i = r.ifAlreadyInstalled;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/ifAlreadyInstalled",
                              schemaPath: "#/oneOf/10/properties/ifAlreadyInstalled/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (!(i === "overwrite" || i === "skip" || i === "error"))
                          return o.errors = [
                            {
                              instancePath: t + "/ifAlreadyInstalled",
                              schemaPath: "#/oneOf/10/properties/ifAlreadyInstalled/enum",
                              keyword: "enum",
                              params: {
                                allowedValues: tr.oneOf[10].properties.ifAlreadyInstalled.enum
                              },
                              message: "must be equal to one of the allowed values"
                            }
                          ], !1;
                        var w = f === e;
                      } else
                        var w = !0;
                      if (w) {
                        if (r.step !== void 0) {
                          let i = r.step;
                          const f = e;
                          if (typeof i != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/step",
                                schemaPath: "#/oneOf/10/properties/step/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          if (i !== "installTheme")
                            return o.errors = [
                              {
                                instancePath: t + "/step",
                                schemaPath: "#/oneOf/10/properties/step/const",
                                keyword: "const",
                                params: {
                                  allowedValue: "installTheme"
                                },
                                message: "must be equal to constant"
                              }
                            ], !1;
                          var w = f === e;
                        } else
                          var w = !0;
                        if (w) {
                          if (r.themeData !== void 0) {
                            let i = r.themeData;
                            const f = e, j = e;
                            let a = !1;
                            const A = e;
                            Y(
                              i,
                              {
                                instancePath: t + "/themeData",
                                parentData: r,
                                parentDataProperty: "themeData",
                                rootData: u
                              }
                            ) || (s = s === null ? Y.errors : s.concat(
                              Y.errors
                            ), e = s.length);
                            var R = A === e;
                            if (a = a || R, !a) {
                              const J = e;
                              ce(
                                i,
                                {
                                  instancePath: t + "/themeData",
                                  parentData: r,
                                  parentDataProperty: "themeData",
                                  rootData: u
                                }
                              ) || (s = s === null ? ce.errors : s.concat(
                                ce.errors
                              ), e = s.length);
                              var R = J === e;
                              a = a || R;
                            }
                            if (a)
                              e = j, s !== null && (j ? s.length = j : s = null);
                            else {
                              const J = {
                                instancePath: t + "/themeData",
                                schemaPath: "#/oneOf/10/properties/themeData/anyOf",
                                keyword: "anyOf",
                                params: {},
                                message: "must match a schema in anyOf"
                              };
                              return s === null ? s = [
                                J
                              ] : s.push(
                                J
                              ), e++, o.errors = s, !1;
                            }
                            var w = f === e;
                          } else
                            var w = !0;
                          if (w) {
                            if (r.themeZipFile !== void 0) {
                              const i = e;
                              Y(
                                r.themeZipFile,
                                {
                                  instancePath: t + "/themeZipFile",
                                  parentData: r,
                                  parentDataProperty: "themeZipFile",
                                  rootData: u
                                }
                              ) || (s = s === null ? Y.errors : s.concat(
                                Y.errors
                              ), e = s.length);
                              var w = i === e;
                            } else
                              var w = !0;
                            if (w)
                              if (r.options !== void 0) {
                                let i = r.options;
                                const f = e;
                                if (e === e)
                                  if (i && typeof i == "object" && !Array.isArray(
                                    i
                                  )) {
                                    const A = e;
                                    for (const se in i)
                                      if (!(se === "activate" || se === "importStarterContent" || se === "targetFolderName"))
                                        return o.errors = [
                                          {
                                            instancePath: t + "/options",
                                            schemaPath: "#/definitions/InstallThemeOptions/additionalProperties",
                                            keyword: "additionalProperties",
                                            params: {
                                              additionalProperty: se
                                            },
                                            message: "must NOT have additional properties"
                                          }
                                        ], !1;
                                    if (A === e) {
                                      if (i.activate !== void 0) {
                                        const se = e;
                                        if (typeof i.activate != "boolean")
                                          return o.errors = [
                                            {
                                              instancePath: t + "/options/activate",
                                              schemaPath: "#/definitions/InstallThemeOptions/properties/activate/type",
                                              keyword: "type",
                                              params: {
                                                type: "boolean"
                                              },
                                              message: "must be boolean"
                                            }
                                          ], !1;
                                        var L = se === e;
                                      } else
                                        var L = !0;
                                      if (L) {
                                        if (i.importStarterContent !== void 0) {
                                          const se = e;
                                          if (typeof i.importStarterContent != "boolean")
                                            return o.errors = [
                                              {
                                                instancePath: t + "/options/importStarterContent",
                                                schemaPath: "#/definitions/InstallThemeOptions/properties/importStarterContent/type",
                                                keyword: "type",
                                                params: {
                                                  type: "boolean"
                                                },
                                                message: "must be boolean"
                                              }
                                            ], !1;
                                          var L = se === e;
                                        } else
                                          var L = !0;
                                        if (L)
                                          if (i.targetFolderName !== void 0) {
                                            const se = e;
                                            if (typeof i.targetFolderName != "string")
                                              return o.errors = [
                                                {
                                                  instancePath: t + "/options/targetFolderName",
                                                  schemaPath: "#/definitions/InstallThemeOptions/properties/targetFolderName/type",
                                                  keyword: "type",
                                                  params: {
                                                    type: "string"
                                                  },
                                                  message: "must be string"
                                                }
                                              ], !1;
                                            var L = se === e;
                                          } else
                                            var L = !0;
                                      }
                                    }
                                  } else
                                    return o.errors = [
                                      {
                                        instancePath: t + "/options",
                                        schemaPath: "#/definitions/InstallThemeOptions/type",
                                        keyword: "type",
                                        params: {
                                          type: "object"
                                        },
                                        message: "must be object"
                                      }
                                    ], !1;
                                var w = f === e;
                              } else
                                var w = !0;
                          }
                        }
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/10/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "login") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/11/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "username" || i === "password"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/11/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/11/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/11/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var U = A === e;
                            } else
                              var U = !0;
                            if (U)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/11/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var U = a === e;
                              } else
                                var U = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/11/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var F = f === e;
                    } else
                      var F = !0;
                    if (F) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/11/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "login")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/11/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "login"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var F = f === e;
                      } else
                        var F = !0;
                      if (F) {
                        if (r.username !== void 0) {
                          const i = e;
                          if (typeof r.username != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/username",
                                schemaPath: "#/oneOf/11/properties/username/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var F = i === e;
                        } else
                          var F = !0;
                        if (F)
                          if (r.password !== void 0) {
                            const i = e;
                            if (typeof r.password != "string")
                              return o.errors = [
                                {
                                  instancePath: t + "/password",
                                  schemaPath: "#/oneOf/11/properties/password/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var F = i === e;
                          } else
                            var F = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/11/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "mkdir") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.path === void 0 && (c = "path") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/12/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "path"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/12/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/12/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/12/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var I = A === e;
                            } else
                              var I = !0;
                            if (I)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/12/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var I = a === e;
                              } else
                                var I = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/12/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var C = f === e;
                    } else
                      var C = !0;
                    if (C) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/12/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "mkdir")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/12/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "mkdir"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var C = f === e;
                      } else
                        var C = !0;
                      if (C)
                        if (r.path !== void 0) {
                          const i = e;
                          if (typeof r.path != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/path",
                                schemaPath: "#/oneOf/12/properties/path/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var C = i === e;
                        } else
                          var C = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/12/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "mv") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.fromPath === void 0 && (c = "fromPath") || r.step === void 0 && (c = "step") || r.toPath === void 0 && (c = "toPath"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/13/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "fromPath" || i === "toPath"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/13/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/13/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/13/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var D = A === e;
                            } else
                              var D = !0;
                            if (D)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/13/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var D = a === e;
                              } else
                                var D = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/13/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var ee = f === e;
                    } else
                      var ee = !0;
                    if (ee) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/13/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "mv")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/13/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "mv"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var ee = f === e;
                      } else
                        var ee = !0;
                      if (ee) {
                        if (r.fromPath !== void 0) {
                          const i = e;
                          if (typeof r.fromPath != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/fromPath",
                                schemaPath: "#/oneOf/13/properties/fromPath/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var ee = i === e;
                        } else
                          var ee = !0;
                        if (ee)
                          if (r.toPath !== void 0) {
                            const i = e;
                            if (typeof r.toPath != "string")
                              return o.errors = [
                                {
                                  instancePath: t + "/toPath",
                                  schemaPath: "#/oneOf/13/properties/toPath/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var ee = i === e;
                          } else
                            var ee = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/13/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "resetData") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/14/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/14/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/14/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/14/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var S = A === e;
                            } else
                              var S = !0;
                            if (S)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/14/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var S = a === e;
                              } else
                                var S = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/14/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var H = f === e;
                    } else
                      var H = !0;
                    if (H)
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/14/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "resetData")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/14/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "resetData"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var H = f === e;
                      } else
                        var H = !0;
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/14/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "request") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.request === void 0 && (c = "request") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/15/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "request"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/15/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/15/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/15/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var M = A === e;
                            } else
                              var M = !0;
                            if (M)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/15/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var M = a === e;
                              } else
                                var M = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/15/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var z = f === e;
                    } else
                      var z = !0;
                    if (z) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/15/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "request")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/15/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "request"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var z = f === e;
                      } else
                        var z = !0;
                      if (z)
                        if (r.request !== void 0) {
                          const i = e;
                          ge(
                            r.request,
                            {
                              instancePath: t + "/request",
                              parentData: r,
                              parentDataProperty: "request",
                              rootData: u
                            }
                          ) || (s = s === null ? ge.errors : s.concat(
                            ge.errors
                          ), e = s.length);
                          var z = i === e;
                        } else
                          var z = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/15/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "rm") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.path === void 0 && (c = "path") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/16/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "path"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/16/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/16/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/16/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var oe = A === e;
                            } else
                              var oe = !0;
                            if (oe)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/16/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var oe = a === e;
                              } else
                                var oe = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/16/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var ue = f === e;
                    } else
                      var ue = !0;
                    if (ue) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/16/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "rm")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/16/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "rm"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var ue = f === e;
                      } else
                        var ue = !0;
                      if (ue)
                        if (r.path !== void 0) {
                          const i = e;
                          if (typeof r.path != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/path",
                                schemaPath: "#/oneOf/16/properties/path/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var ue = i === e;
                        } else
                          var ue = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/16/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "rmdir") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.path === void 0 && (c = "path") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/17/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "path"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/17/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/17/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/17/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var Pe = A === e;
                            } else
                              var Pe = !0;
                            if (Pe)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/17/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var Pe = a === e;
                              } else
                                var Pe = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/17/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var qe = f === e;
                    } else
                      var qe = !0;
                    if (qe) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/17/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "rmdir")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/17/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "rmdir"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var qe = f === e;
                      } else
                        var qe = !0;
                      if (qe)
                        if (r.path !== void 0) {
                          const i = e;
                          if (typeof r.path != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/path",
                                schemaPath: "#/oneOf/17/properties/path/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var qe = i === e;
                        } else
                          var qe = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/17/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "runPHP") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.code === void 0 && (c = "code") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/18/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "code"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/18/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/18/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/18/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var We = A === e;
                            } else
                              var We = !0;
                            if (We)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/18/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var We = a === e;
                              } else
                                var We = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/18/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var Ae = f === e;
                    } else
                      var Ae = !0;
                    if (Ae) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/18/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "runPHP")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/18/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "runPHP"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var Ae = f === e;
                      } else
                        var Ae = !0;
                      if (Ae)
                        if (r.code !== void 0) {
                          let i = r.code;
                          const f = e, j = e;
                          let a = !1;
                          const A = e;
                          if (typeof i != "string") {
                            const J = {
                              instancePath: t + "/code",
                              schemaPath: "#/oneOf/18/properties/code/anyOf/0/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            };
                            s === null ? s = [J] : s.push(J), e++;
                          }
                          var nr = A === e;
                          if (a = a || nr, !a) {
                            const J = e;
                            if (e === J)
                              if (i && typeof i == "object" && !Array.isArray(
                                i
                              )) {
                                let le;
                                if (i.filename === void 0 && (le = "filename") || i.content === void 0 && (le = "content")) {
                                  const je = {
                                    instancePath: t + "/code",
                                    schemaPath: "#/oneOf/18/properties/code/anyOf/1/required",
                                    keyword: "required",
                                    params: {
                                      missingProperty: le
                                    },
                                    message: "must have required property '" + le + "'"
                                  };
                                  s === null ? s = [
                                    je
                                  ] : s.push(
                                    je
                                  ), e++;
                                } else {
                                  const je = e;
                                  for (const ne in i)
                                    if (!(ne === "filename" || ne === "content")) {
                                      const Ee = {
                                        instancePath: t + "/code",
                                        schemaPath: "#/oneOf/18/properties/code/anyOf/1/additionalProperties",
                                        keyword: "additionalProperties",
                                        params: {
                                          additionalProperty: ne
                                        },
                                        message: "must NOT have additional properties"
                                      };
                                      s === null ? s = [
                                        Ee
                                      ] : s.push(
                                        Ee
                                      ), e++;
                                      break;
                                    }
                                  if (je === e) {
                                    if (i.filename !== void 0) {
                                      const ne = e;
                                      if (typeof i.filename != "string") {
                                        const Ee = {
                                          instancePath: t + "/code/filename",
                                          schemaPath: "#/oneOf/18/properties/code/anyOf/1/properties/filename/type",
                                          keyword: "type",
                                          params: {
                                            type: "string"
                                          },
                                          message: "must be string"
                                        };
                                        s === null ? s = [
                                          Ee
                                        ] : s.push(
                                          Ee
                                        ), e++;
                                      }
                                      var Be = ne === e;
                                    } else
                                      var Be = !0;
                                    if (Be)
                                      if (i.content !== void 0) {
                                        const ne = e;
                                        if (typeof i.content != "string") {
                                          const Q = {
                                            instancePath: t + "/code/content",
                                            schemaPath: "#/oneOf/18/properties/code/anyOf/1/properties/content/type",
                                            keyword: "type",
                                            params: {
                                              type: "string"
                                            },
                                            message: "must be string"
                                          };
                                          s === null ? s = [
                                            Q
                                          ] : s.push(
                                            Q
                                          ), e++;
                                        }
                                        var Be = ne === e;
                                      } else
                                        var Be = !0;
                                  }
                                }
                              } else {
                                const le = {
                                  instancePath: t + "/code",
                                  schemaPath: "#/oneOf/18/properties/code/anyOf/1/type",
                                  keyword: "type",
                                  params: {
                                    type: "object"
                                  },
                                  message: "must be object"
                                };
                                s === null ? s = [
                                  le
                                ] : s.push(
                                  le
                                ), e++;
                              }
                            var nr = J === e;
                            a = a || nr;
                          }
                          if (a)
                            e = j, s !== null && (j ? s.length = j : s = null);
                          else {
                            const J = {
                              instancePath: t + "/code",
                              schemaPath: "#/oneOf/18/properties/code/anyOf",
                              keyword: "anyOf",
                              params: {},
                              message: "must match a schema in anyOf"
                            };
                            return s === null ? s = [J] : s.push(J), e++, o.errors = s, !1;
                          }
                          var Ae = f === e;
                        } else
                          var Ae = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/18/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "runPHPWithOptions") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.options === void 0 && (c = "options") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/19/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "options"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/19/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/19/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/19/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var Me = A === e;
                            } else
                              var Me = !0;
                            if (Me)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/19/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var Me = a === e;
                              } else
                                var Me = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/19/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var Re = f === e;
                    } else
                      var Re = !0;
                    if (Re) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/19/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "runPHPWithOptions")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/19/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "runPHPWithOptions"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var Re = f === e;
                      } else
                        var Re = !0;
                      if (Re)
                        if (r.options !== void 0) {
                          const i = e;
                          pe(
                            r.options,
                            {
                              instancePath: t + "/options",
                              parentData: r,
                              parentDataProperty: "options",
                              rootData: u
                            }
                          ) || (s = s === null ? pe.errors : s.concat(
                            pe.errors
                          ), e = s.length);
                          var Re = i === e;
                        } else
                          var Re = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/19/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "runWpInstallationWizard") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.options === void 0 && (c = "options") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/20/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "options"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/20/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/20/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/20/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var ze = A === e;
                            } else
                              var ze = !0;
                            if (ze)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/20/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var ze = a === e;
                              } else
                                var ze = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/20/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var Se = f === e;
                    } else
                      var Se = !0;
                    if (Se) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/20/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "runWpInstallationWizard")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/20/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "runWpInstallationWizard"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var Se = f === e;
                      } else
                        var Se = !0;
                      if (Se)
                        if (r.options !== void 0) {
                          let i = r.options;
                          const f = e;
                          if (e === e)
                            if (i && typeof i == "object" && !Array.isArray(
                              i
                            )) {
                              const A = e;
                              for (const se in i)
                                if (!(se === "adminUsername" || se === "adminPassword"))
                                  return o.errors = [
                                    {
                                      instancePath: t + "/options",
                                      schemaPath: "#/definitions/WordPressInstallationOptions/additionalProperties",
                                      keyword: "additionalProperties",
                                      params: {
                                        additionalProperty: se
                                      },
                                      message: "must NOT have additional properties"
                                    }
                                  ], !1;
                              if (A === e) {
                                if (i.adminUsername !== void 0) {
                                  const se = e;
                                  if (typeof i.adminUsername != "string")
                                    return o.errors = [
                                      {
                                        instancePath: t + "/options/adminUsername",
                                        schemaPath: "#/definitions/WordPressInstallationOptions/properties/adminUsername/type",
                                        keyword: "type",
                                        params: {
                                          type: "string"
                                        },
                                        message: "must be string"
                                      }
                                    ], !1;
                                  var Ve = se === e;
                                } else
                                  var Ve = !0;
                                if (Ve)
                                  if (i.adminPassword !== void 0) {
                                    const se = e;
                                    if (typeof i.adminPassword != "string")
                                      return o.errors = [
                                        {
                                          instancePath: t + "/options/adminPassword",
                                          schemaPath: "#/definitions/WordPressInstallationOptions/properties/adminPassword/type",
                                          keyword: "type",
                                          params: {
                                            type: "string"
                                          },
                                          message: "must be string"
                                        }
                                      ], !1;
                                    var Ve = se === e;
                                  } else
                                    var Ve = !0;
                              }
                            } else
                              return o.errors = [
                                {
                                  instancePath: t + "/options",
                                  schemaPath: "#/definitions/WordPressInstallationOptions/type",
                                  keyword: "type",
                                  params: {
                                    type: "object"
                                  },
                                  message: "must be object"
                                }
                              ], !1;
                          var Se = f === e;
                        } else
                          var Se = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/20/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "runSql") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.sql === void 0 && (c = "sql") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/21/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "sql"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/21/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/21/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/21/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var He = A === e;
                            } else
                              var He = !0;
                            if (He)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/21/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var He = a === e;
                              } else
                                var He = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/21/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var Le = f === e;
                    } else
                      var Le = !0;
                    if (Le) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/21/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "runSql")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/21/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "runSql"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var Le = f === e;
                      } else
                        var Le = !0;
                      if (Le)
                        if (r.sql !== void 0) {
                          const i = e;
                          Y(r.sql, {
                            instancePath: t + "/sql",
                            parentData: r,
                            parentDataProperty: "sql",
                            rootData: u
                          }) || (s = s === null ? Y.errors : s.concat(
                            Y.errors
                          ), e = s.length);
                          var Le = i === e;
                        } else
                          var Le = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/21/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "setSiteOptions") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.options === void 0 && (c = "options") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/22/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "options"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/22/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/22/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/22/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var Ze = A === e;
                            } else
                              var Ze = !0;
                            if (Ze)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/22/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var Ze = a === e;
                              } else
                                var Ze = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/22/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var Fe = f === e;
                    } else
                      var Fe = !0;
                    if (Fe) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/22/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "setSiteOptions")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/22/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "setSiteOptions"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var Fe = f === e;
                      } else
                        var Fe = !0;
                      if (Fe)
                        if (r.options !== void 0) {
                          let i = r.options;
                          const f = e;
                          if (e === f && !(i && typeof i == "object" && !Array.isArray(
                            i
                          )))
                            return o.errors = [
                              {
                                instancePath: t + "/options",
                                schemaPath: "#/oneOf/22/properties/options/type",
                                keyword: "type",
                                params: {
                                  type: "object"
                                },
                                message: "must be object"
                              }
                            ], !1;
                          var Fe = f === e;
                        } else
                          var Fe = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/22/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "unzip") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.extractToPath === void 0 && (c = "extractToPath") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/23/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "zipFile" || i === "zipPath" || i === "extractToPath"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/23/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/23/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/23/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var Qe = A === e;
                            } else
                              var Qe = !0;
                            if (Qe)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/23/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var Qe = a === e;
                              } else
                                var Qe = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/23/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var me = f === e;
                    } else
                      var me = !0;
                    if (me) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/23/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "unzip")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/23/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "unzip"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var me = f === e;
                      } else
                        var me = !0;
                      if (me) {
                        if (r.zipFile !== void 0) {
                          const i = e;
                          Y(
                            r.zipFile,
                            {
                              instancePath: t + "/zipFile",
                              parentData: r,
                              parentDataProperty: "zipFile",
                              rootData: u
                            }
                          ) || (s = s === null ? Y.errors : s.concat(
                            Y.errors
                          ), e = s.length);
                          var me = i === e;
                        } else
                          var me = !0;
                        if (me) {
                          if (r.zipPath !== void 0) {
                            const i = e;
                            if (typeof r.zipPath != "string")
                              return o.errors = [
                                {
                                  instancePath: t + "/zipPath",
                                  schemaPath: "#/oneOf/23/properties/zipPath/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var me = i === e;
                          } else
                            var me = !0;
                          if (me)
                            if (r.extractToPath !== void 0) {
                              const i = e;
                              if (typeof r.extractToPath != "string")
                                return o.errors = [
                                  {
                                    instancePath: t + "/extractToPath",
                                    schemaPath: "#/oneOf/23/properties/extractToPath/type",
                                    keyword: "type",
                                    params: {
                                      type: "string"
                                    },
                                    message: "must be string"
                                  }
                                ], !1;
                              var me = i === e;
                            } else
                              var me = !0;
                        }
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/23/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "updateUserMeta") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.meta === void 0 && (c = "meta") || r.step === void 0 && (c = "step") || r.userId === void 0 && (c = "userId"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/24/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "meta" || i === "userId"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/24/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/24/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/24/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var Ye = A === e;
                            } else
                              var Ye = !0;
                            if (Ye)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/24/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var Ye = a === e;
                              } else
                                var Ye = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/24/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var _e = f === e;
                    } else
                      var _e = !0;
                    if (_e) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/24/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "updateUserMeta")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/24/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "updateUserMeta"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var _e = f === e;
                      } else
                        var _e = !0;
                      if (_e) {
                        if (r.meta !== void 0) {
                          let i = r.meta;
                          const f = e;
                          if (e === f && !(i && typeof i == "object" && !Array.isArray(
                            i
                          )))
                            return o.errors = [
                              {
                                instancePath: t + "/meta",
                                schemaPath: "#/oneOf/24/properties/meta/type",
                                keyword: "type",
                                params: {
                                  type: "object"
                                },
                                message: "must be object"
                              }
                            ], !1;
                          var _e = f === e;
                        } else
                          var _e = !0;
                        if (_e)
                          if (r.userId !== void 0) {
                            let i = r.userId;
                            const f = e;
                            if (!(typeof i == "number" && isFinite(
                              i
                            )))
                              return o.errors = [
                                {
                                  instancePath: t + "/userId",
                                  schemaPath: "#/oneOf/24/properties/userId/type",
                                  keyword: "type",
                                  params: {
                                    type: "number"
                                  },
                                  message: "must be number"
                                }
                              ], !1;
                            var _e = f === e;
                          } else
                            var _e = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/24/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "writeFile") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.data === void 0 && (c = "data") || r.path === void 0 && (c = "path") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/25/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "path" || i === "data"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/25/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/25/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/25/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var Ge = A === e;
                            } else
                              var Ge = !0;
                            if (Ge)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/25/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var Ge = a === e;
                              } else
                                var Ge = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/25/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var ke = f === e;
                    } else
                      var ke = !0;
                    if (ke) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/25/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "writeFile")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/25/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "writeFile"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var ke = f === e;
                      } else
                        var ke = !0;
                      if (ke) {
                        if (r.path !== void 0) {
                          const i = e;
                          if (typeof r.path != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/path",
                                schemaPath: "#/oneOf/25/properties/path/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var ke = i === e;
                        } else
                          var ke = !0;
                        if (ke)
                          if (r.data !== void 0) {
                            let i = r.data;
                            const f = e, j = e;
                            let a = !1;
                            const A = e;
                            Y(
                              i,
                              {
                                instancePath: t + "/data",
                                parentData: r,
                                parentDataProperty: "data",
                                rootData: u
                              }
                            ) || (s = s === null ? Y.errors : s.concat(
                              Y.errors
                            ), e = s.length);
                            var Ce = A === e;
                            if (a = a || Ce, !a) {
                              const J = e;
                              if (typeof i != "string") {
                                const le = {
                                  instancePath: t + "/data",
                                  schemaPath: "#/oneOf/25/properties/data/anyOf/1/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                };
                                s === null ? s = [
                                  le
                                ] : s.push(
                                  le
                                ), e++;
                              }
                              var Ce = J === e;
                              if (a = a || Ce, !a) {
                                const le = e;
                                if (e === le)
                                  if (i && typeof i == "object" && !Array.isArray(
                                    i
                                  )) {
                                    let ne;
                                    if (i.BYTES_PER_ELEMENT === void 0 && (ne = "BYTES_PER_ELEMENT") || i.buffer === void 0 && (ne = "buffer") || i.byteLength === void 0 && (ne = "byteLength") || i.byteOffset === void 0 && (ne = "byteOffset") || i.length === void 0 && (ne = "length")) {
                                      const Ee = {
                                        instancePath: t + "/data",
                                        schemaPath: "#/oneOf/25/properties/data/anyOf/2/required",
                                        keyword: "required",
                                        params: {
                                          missingProperty: ne
                                        },
                                        message: "must have required property '" + ne + "'"
                                      };
                                      s === null ? s = [
                                        Ee
                                      ] : s.push(
                                        Ee
                                      ), e++;
                                    } else {
                                      const Ee = e;
                                      for (const Q in i)
                                        if (!(Q === "BYTES_PER_ELEMENT" || Q === "buffer" || Q === "byteLength" || Q === "byteOffset" || Q === "length")) {
                                          let he = i[Q];
                                          const er = e;
                                          if (!(typeof he == "number" && isFinite(
                                            he
                                          ))) {
                                            const fe = {
                                              instancePath: t + "/data/" + Q.replace(
                                                /~/g,
                                                "~0"
                                              ).replace(
                                                /\//g,
                                                "~1"
                                              ),
                                              schemaPath: "#/oneOf/25/properties/data/anyOf/2/additionalProperties/type",
                                              keyword: "type",
                                              params: {
                                                type: "number"
                                              },
                                              message: "must be number"
                                            };
                                            s === null ? s = [
                                              fe
                                            ] : s.push(
                                              fe
                                            ), e++;
                                          }
                                          var Hr = er === e;
                                          if (!Hr)
                                            break;
                                        }
                                      if (Ee === e) {
                                        if (i.BYTES_PER_ELEMENT !== void 0) {
                                          let Q = i.BYTES_PER_ELEMENT;
                                          const he = e;
                                          if (!(typeof Q == "number" && isFinite(
                                            Q
                                          ))) {
                                            const er = {
                                              instancePath: t + "/data/BYTES_PER_ELEMENT",
                                              schemaPath: "#/oneOf/25/properties/data/anyOf/2/properties/BYTES_PER_ELEMENT/type",
                                              keyword: "type",
                                              params: {
                                                type: "number"
                                              },
                                              message: "must be number"
                                            };
                                            s === null ? s = [
                                              er
                                            ] : s.push(
                                              er
                                            ), e++;
                                          }
                                          var ye = he === e;
                                        } else
                                          var ye = !0;
                                        if (ye) {
                                          if (i.buffer !== void 0) {
                                            let Q = i.buffer;
                                            const he = e;
                                            if (e === he)
                                              if (Q && typeof Q == "object" && !Array.isArray(
                                                Q
                                              )) {
                                                let fe;
                                                if (Q.byteLength === void 0 && (fe = "byteLength")) {
                                                  const rr = {
                                                    instancePath: t + "/data/buffer",
                                                    schemaPath: "#/oneOf/25/properties/data/anyOf/2/properties/buffer/required",
                                                    keyword: "required",
                                                    params: {
                                                      missingProperty: fe
                                                    },
                                                    message: "must have required property '" + fe + "'"
                                                  };
                                                  s === null ? s = [
                                                    rr
                                                  ] : s.push(
                                                    rr
                                                  ), e++;
                                                } else {
                                                  const rr = e;
                                                  for (const De in Q)
                                                    if (De !== "byteLength") {
                                                      const Ue = {
                                                        instancePath: t + "/data/buffer",
                                                        schemaPath: "#/oneOf/25/properties/data/anyOf/2/properties/buffer/additionalProperties",
                                                        keyword: "additionalProperties",
                                                        params: {
                                                          additionalProperty: De
                                                        },
                                                        message: "must NOT have additional properties"
                                                      };
                                                      s === null ? s = [
                                                        Ue
                                                      ] : s.push(
                                                        Ue
                                                      ), e++;
                                                      break;
                                                    }
                                                  if (rr === e && Q.byteLength !== void 0) {
                                                    let De = Q.byteLength;
                                                    if (!(typeof De == "number" && isFinite(
                                                      De
                                                    ))) {
                                                      const Ue = {
                                                        instancePath: t + "/data/buffer/byteLength",
                                                        schemaPath: "#/oneOf/25/properties/data/anyOf/2/properties/buffer/properties/byteLength/type",
                                                        keyword: "type",
                                                        params: {
                                                          type: "number"
                                                        },
                                                        message: "must be number"
                                                      };
                                                      s === null ? s = [
                                                        Ue
                                                      ] : s.push(
                                                        Ue
                                                      ), e++;
                                                    }
                                                  }
                                                }
                                              } else {
                                                const fe = {
                                                  instancePath: t + "/data/buffer",
                                                  schemaPath: "#/oneOf/25/properties/data/anyOf/2/properties/buffer/type",
                                                  keyword: "type",
                                                  params: {
                                                    type: "object"
                                                  },
                                                  message: "must be object"
                                                };
                                                s === null ? s = [
                                                  fe
                                                ] : s.push(
                                                  fe
                                                ), e++;
                                              }
                                            var ye = he === e;
                                          } else
                                            var ye = !0;
                                          if (ye) {
                                            if (i.byteLength !== void 0) {
                                              let Q = i.byteLength;
                                              const he = e;
                                              if (!(typeof Q == "number" && isFinite(
                                                Q
                                              ))) {
                                                const fe = {
                                                  instancePath: t + "/data/byteLength",
                                                  schemaPath: "#/oneOf/25/properties/data/anyOf/2/properties/byteLength/type",
                                                  keyword: "type",
                                                  params: {
                                                    type: "number"
                                                  },
                                                  message: "must be number"
                                                };
                                                s === null ? s = [
                                                  fe
                                                ] : s.push(
                                                  fe
                                                ), e++;
                                              }
                                              var ye = he === e;
                                            } else
                                              var ye = !0;
                                            if (ye) {
                                              if (i.byteOffset !== void 0) {
                                                let Q = i.byteOffset;
                                                const he = e;
                                                if (!(typeof Q == "number" && isFinite(
                                                  Q
                                                ))) {
                                                  const fe = {
                                                    instancePath: t + "/data/byteOffset",
                                                    schemaPath: "#/oneOf/25/properties/data/anyOf/2/properties/byteOffset/type",
                                                    keyword: "type",
                                                    params: {
                                                      type: "number"
                                                    },
                                                    message: "must be number"
                                                  };
                                                  s === null ? s = [
                                                    fe
                                                  ] : s.push(
                                                    fe
                                                  ), e++;
                                                }
                                                var ye = he === e;
                                              } else
                                                var ye = !0;
                                              if (ye)
                                                if (i.length !== void 0) {
                                                  let Q = i.length;
                                                  const he = e;
                                                  if (!(typeof Q == "number" && isFinite(
                                                    Q
                                                  ))) {
                                                    const fe = {
                                                      instancePath: t + "/data/length",
                                                      schemaPath: "#/oneOf/25/properties/data/anyOf/2/properties/length/type",
                                                      keyword: "type",
                                                      params: {
                                                        type: "number"
                                                      },
                                                      message: "must be number"
                                                    };
                                                    s === null ? s = [
                                                      fe
                                                    ] : s.push(
                                                      fe
                                                    ), e++;
                                                  }
                                                  var ye = he === e;
                                                } else
                                                  var ye = !0;
                                            }
                                          }
                                        }
                                      }
                                    }
                                  } else {
                                    const ne = {
                                      instancePath: t + "/data",
                                      schemaPath: "#/oneOf/25/properties/data/anyOf/2/type",
                                      keyword: "type",
                                      params: {
                                        type: "object"
                                      },
                                      message: "must be object"
                                    };
                                    s === null ? s = [
                                      ne
                                    ] : s.push(
                                      ne
                                    ), e++;
                                  }
                                var Ce = le === e;
                                a = a || Ce;
                              }
                            }
                            if (a)
                              e = j, s !== null && (j ? s.length = j : s = null);
                            else {
                              const J = {
                                instancePath: t + "/data",
                                schemaPath: "#/oneOf/25/properties/data/anyOf",
                                keyword: "anyOf",
                                params: {},
                                message: "must match a schema in anyOf"
                              };
                              return s === null ? s = [
                                J
                              ] : s.push(
                                J
                              ), e++, o.errors = s, !1;
                            }
                            var ke = f === e;
                          } else
                            var ke = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/25/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "writeFiles") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.filesTree === void 0 && (c = "filesTree") || r.step === void 0 && (c = "step") || r.writeToPath === void 0 && (c = "writeToPath"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/26/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "writeToPath" || i === "filesTree"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/26/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/26/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/26/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var Je = A === e;
                            } else
                              var Je = !0;
                            if (Je)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/26/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var Je = a === e;
                              } else
                                var Je = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/26/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var Oe = f === e;
                    } else
                      var Oe = !0;
                    if (Oe) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/26/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "writeFiles")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/26/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "writeFiles"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var Oe = f === e;
                      } else
                        var Oe = !0;
                      if (Oe) {
                        if (r.writeToPath !== void 0) {
                          const i = e;
                          if (typeof r.writeToPath != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/writeToPath",
                                schemaPath: "#/oneOf/26/properties/writeToPath/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var Oe = i === e;
                        } else
                          var Oe = !0;
                        if (Oe)
                          if (r.filesTree !== void 0) {
                            const i = e;
                            ce(
                              r.filesTree,
                              {
                                instancePath: t + "/filesTree",
                                parentData: r,
                                parentDataProperty: "filesTree",
                                rootData: u
                              }
                            ) || (s = s === null ? ce.errors : s.concat(
                              ce.errors
                            ), e = s.length);
                            var Oe = i === e;
                          } else
                            var Oe = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/26/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "wp-cli") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.command === void 0 && (c = "command") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/27/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "command" || i === "wpCliPath"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/27/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/27/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/27/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var Xe = A === e;
                            } else
                              var Xe = !0;
                            if (Xe)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/27/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var Xe = a === e;
                              } else
                                var Xe = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/27/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var Te = f === e;
                    } else
                      var Te = !0;
                    if (Te) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/27/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "wp-cli")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/27/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "wp-cli"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var Te = f === e;
                      } else
                        var Te = !0;
                      if (Te) {
                        if (r.command !== void 0) {
                          let i = r.command;
                          const f = e, j = e;
                          let a = !1;
                          const A = e;
                          if (typeof i != "string") {
                            const J = {
                              instancePath: t + "/command",
                              schemaPath: "#/oneOf/27/properties/command/anyOf/0/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            };
                            s === null ? s = [J] : s.push(J), e++;
                          }
                          var pr = A === e;
                          if (a = a || pr, !a) {
                            const J = e;
                            if (e === J)
                              if (Array.isArray(
                                i
                              )) {
                                var _r = !0;
                                const le = i.length;
                                for (let je = 0; je < le; je++) {
                                  const ne = e;
                                  if (typeof i[je] != "string") {
                                    const Q = {
                                      instancePath: t + "/command/" + je,
                                      schemaPath: "#/oneOf/27/properties/command/anyOf/1/items/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    };
                                    s === null ? s = [
                                      Q
                                    ] : s.push(
                                      Q
                                    ), e++;
                                  }
                                  var _r = ne === e;
                                  if (!_r)
                                    break;
                                }
                              } else {
                                const le = {
                                  instancePath: t + "/command",
                                  schemaPath: "#/oneOf/27/properties/command/anyOf/1/type",
                                  keyword: "type",
                                  params: {
                                    type: "array"
                                  },
                                  message: "must be array"
                                };
                                s === null ? s = [
                                  le
                                ] : s.push(
                                  le
                                ), e++;
                              }
                            var pr = J === e;
                            a = a || pr;
                          }
                          if (a)
                            e = j, s !== null && (j ? s.length = j : s = null);
                          else {
                            const J = {
                              instancePath: t + "/command",
                              schemaPath: "#/oneOf/27/properties/command/anyOf",
                              keyword: "anyOf",
                              params: {},
                              message: "must match a schema in anyOf"
                            };
                            return s === null ? s = [J] : s.push(J), e++, o.errors = s, !1;
                          }
                          var Te = f === e;
                        } else
                          var Te = !0;
                        if (Te)
                          if (r.wpCliPath !== void 0) {
                            const i = e;
                            if (typeof r.wpCliPath != "string")
                              return o.errors = [
                                {
                                  instancePath: t + "/wpCliPath",
                                  schemaPath: "#/oneOf/27/properties/wpCliPath/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var Te = i === e;
                          } else
                            var Te = !0;
                      }
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/27/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else if (G === "setSiteLanguage") {
            if (e === e)
              if (r && typeof r == "object" && !Array.isArray(r)) {
                let c;
                if (r.language === void 0 && (c = "language") || r.step === void 0 && (c = "step"))
                  return o.errors = [
                    {
                      instancePath: t,
                      schemaPath: "#/oneOf/28/required",
                      keyword: "required",
                      params: {
                        missingProperty: c
                      },
                      message: "must have required property '" + c + "'"
                    }
                  ], !1;
                {
                  const x = e;
                  for (const i in r)
                    if (!(i === "progress" || i === "step" || i === "language"))
                      return o.errors = [
                        {
                          instancePath: t,
                          schemaPath: "#/oneOf/28/additionalProperties",
                          keyword: "additionalProperties",
                          params: {
                            additionalProperty: i
                          },
                          message: "must NOT have additional properties"
                        }
                      ], !1;
                  if (x === e) {
                    if (r.progress !== void 0) {
                      let i = r.progress;
                      const f = e;
                      if (e === f)
                        if (i && typeof i == "object" && !Array.isArray(i)) {
                          const j = e;
                          for (const a in i)
                            if (!(a === "weight" || a === "caption"))
                              return o.errors = [
                                {
                                  instancePath: t + "/progress",
                                  schemaPath: "#/oneOf/28/properties/progress/additionalProperties",
                                  keyword: "additionalProperties",
                                  params: {
                                    additionalProperty: a
                                  },
                                  message: "must NOT have additional properties"
                                }
                              ], !1;
                          if (j === e) {
                            if (i.weight !== void 0) {
                              let a = i.weight;
                              const A = e;
                              if (!(typeof a == "number" && isFinite(
                                a
                              )))
                                return o.errors = [
                                  {
                                    instancePath: t + "/progress/weight",
                                    schemaPath: "#/oneOf/28/properties/progress/properties/weight/type",
                                    keyword: "type",
                                    params: {
                                      type: "number"
                                    },
                                    message: "must be number"
                                  }
                                ], !1;
                              var Ke = A === e;
                            } else
                              var Ke = !0;
                            if (Ke)
                              if (i.caption !== void 0) {
                                const a = e;
                                if (typeof i.caption != "string")
                                  return o.errors = [
                                    {
                                      instancePath: t + "/progress/caption",
                                      schemaPath: "#/oneOf/28/properties/progress/properties/caption/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var Ke = a === e;
                              } else
                                var Ke = !0;
                          }
                        } else
                          return o.errors = [
                            {
                              instancePath: t + "/progress",
                              schemaPath: "#/oneOf/28/properties/progress/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var Ne = f === e;
                    } else
                      var Ne = !0;
                    if (Ne) {
                      if (r.step !== void 0) {
                        let i = r.step;
                        const f = e;
                        if (typeof i != "string")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/28/properties/step/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        if (i !== "setSiteLanguage")
                          return o.errors = [
                            {
                              instancePath: t + "/step",
                              schemaPath: "#/oneOf/28/properties/step/const",
                              keyword: "const",
                              params: {
                                allowedValue: "setSiteLanguage"
                              },
                              message: "must be equal to constant"
                            }
                          ], !1;
                        var Ne = f === e;
                      } else
                        var Ne = !0;
                      if (Ne)
                        if (r.language !== void 0) {
                          const i = e;
                          if (typeof r.language != "string")
                            return o.errors = [
                              {
                                instancePath: t + "/language",
                                schemaPath: "#/oneOf/28/properties/language/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var Ne = i === e;
                        } else
                          var Ne = !0;
                    }
                  }
                }
              } else
                return o.errors = [
                  {
                    instancePath: t,
                    schemaPath: "#/oneOf/28/type",
                    keyword: "type",
                    params: { type: "object" },
                    message: "must be object"
                  }
                ], !1;
          } else
            return o.errors = [
              {
                instancePath: t,
                schemaPath: "#/discriminator",
                keyword: "discriminator",
                params: {
                  error: "mapping",
                  tag: "step",
                  tagValue: G
                },
                message: 'value of tag "step" must be in oneOf'
              }
            ], !1;
        else
          return o.errors = [
            {
              instancePath: t,
              schemaPath: "#/discriminator",
              keyword: "discriminator",
              params: {
                error: "tag",
                tag: "step",
                tagValue: G
              },
              message: 'tag "step" must be string'
            }
          ], !1;
      }
    } else
      return o.errors = [
        {
          instancePath: t,
          schemaPath: "#/type",
          keyword: "type",
          params: { type: "object" },
          message: "must be object"
        }
      ], !1;
  return o.errors = s, e === 0;
}
function V(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  if (e === 0)
    if (r && typeof r == "object" && !Array.isArray(r)) {
      const re = e;
      for (const m in r)
        if (!Mr.call(hs.properties, m))
          return V.errors = [
            {
              instancePath: t,
              schemaPath: "#/additionalProperties",
              keyword: "additionalProperties",
              params: { additionalProperty: m },
              message: "must NOT have additional properties"
            }
          ], !1;
      if (re === e) {
        if (r.landingPage !== void 0) {
          const m = e;
          if (typeof r.landingPage != "string")
            return V.errors = [
              {
                instancePath: t + "/landingPage",
                schemaPath: "#/properties/landingPage/type",
                keyword: "type",
                params: { type: "string" },
                message: "must be string"
              }
            ], !1;
          var p = m === e;
        } else
          var p = !0;
        if (p) {
          if (r.description !== void 0) {
            const m = e;
            if (typeof r.description != "string")
              return V.errors = [
                {
                  instancePath: t + "/description",
                  schemaPath: "#/properties/description/type",
                  keyword: "type",
                  params: { type: "string" },
                  message: "must be string"
                }
              ], !1;
            var p = m === e;
          } else
            var p = !0;
          if (p) {
            if (r.meta !== void 0) {
              let m = r.meta;
              const N = e;
              if (e === N)
                if (m && typeof m == "object" && !Array.isArray(m)) {
                  let b;
                  if (m.title === void 0 && (b = "title") || m.author === void 0 && (b = "author"))
                    return V.errors = [
                      {
                        instancePath: t + "/meta",
                        schemaPath: "#/properties/meta/required",
                        keyword: "required",
                        params: {
                          missingProperty: b
                        },
                        message: "must have required property '" + b + "'"
                      }
                    ], !1;
                  {
                    const O = e;
                    for (const T in m)
                      if (!(T === "title" || T === "description" || T === "author" || T === "categories"))
                        return V.errors = [
                          {
                            instancePath: t + "/meta",
                            schemaPath: "#/properties/meta/additionalProperties",
                            keyword: "additionalProperties",
                            params: {
                              additionalProperty: T
                            },
                            message: "must NOT have additional properties"
                          }
                        ], !1;
                    if (O === e) {
                      if (m.title !== void 0) {
                        const T = e;
                        if (typeof m.title != "string")
                          return V.errors = [
                            {
                              instancePath: t + "/meta/title",
                              schemaPath: "#/properties/meta/properties/title/type",
                              keyword: "type",
                              params: {
                                type: "string"
                              },
                              message: "must be string"
                            }
                          ], !1;
                        var d = T === e;
                      } else
                        var d = !0;
                      if (d) {
                        if (m.description !== void 0) {
                          const T = e;
                          if (typeof m.description != "string")
                            return V.errors = [
                              {
                                instancePath: t + "/meta/description",
                                schemaPath: "#/properties/meta/properties/description/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          var d = T === e;
                        } else
                          var d = !0;
                        if (d) {
                          if (m.author !== void 0) {
                            const T = e;
                            if (typeof m.author != "string")
                              return V.errors = [
                                {
                                  instancePath: t + "/meta/author",
                                  schemaPath: "#/properties/meta/properties/author/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var d = T === e;
                          } else
                            var d = !0;
                          if (d)
                            if (m.categories !== void 0) {
                              let T = m.categories;
                              const P = e;
                              if (e === P)
                                if (Array.isArray(
                                  T
                                )) {
                                  var y = !0;
                                  const R = T.length;
                                  for (let L = 0; L < R; L++) {
                                    const U = e;
                                    if (typeof T[L] != "string")
                                      return V.errors = [
                                        {
                                          instancePath: t + "/meta/categories/" + L,
                                          schemaPath: "#/properties/meta/properties/categories/items/type",
                                          keyword: "type",
                                          params: {
                                            type: "string"
                                          },
                                          message: "must be string"
                                        }
                                      ], !1;
                                    var y = U === e;
                                    if (!y)
                                      break;
                                  }
                                } else
                                  return V.errors = [
                                    {
                                      instancePath: t + "/meta/categories",
                                      schemaPath: "#/properties/meta/properties/categories/type",
                                      keyword: "type",
                                      params: {
                                        type: "array"
                                      },
                                      message: "must be array"
                                    }
                                  ], !1;
                              var d = P === e;
                            } else
                              var d = !0;
                        }
                      }
                    }
                  }
                } else
                  return V.errors = [
                    {
                      instancePath: t + "/meta",
                      schemaPath: "#/properties/meta/type",
                      keyword: "type",
                      params: { type: "object" },
                      message: "must be object"
                    }
                  ], !1;
              var p = N === e;
            } else
              var p = !0;
            if (p) {
              if (r.preferredVersions !== void 0) {
                let m = r.preferredVersions;
                const N = e;
                if (e === N)
                  if (m && typeof m == "object" && !Array.isArray(m)) {
                    let b;
                    if (m.php === void 0 && (b = "php") || m.wp === void 0 && (b = "wp"))
                      return V.errors = [
                        {
                          instancePath: t + "/preferredVersions",
                          schemaPath: "#/properties/preferredVersions/required",
                          keyword: "required",
                          params: {
                            missingProperty: b
                          },
                          message: "must have required property '" + b + "'"
                        }
                      ], !1;
                    {
                      const O = e;
                      for (const T in m)
                        if (!(T === "php" || T === "wp"))
                          return V.errors = [
                            {
                              instancePath: t + "/preferredVersions",
                              schemaPath: "#/properties/preferredVersions/additionalProperties",
                              keyword: "additionalProperties",
                              params: {
                                additionalProperty: T
                              },
                              message: "must NOT have additional properties"
                            }
                          ], !1;
                      if (O === e) {
                        if (m.php !== void 0) {
                          let T = m.php;
                          const P = e, w = e;
                          let R = !1;
                          const L = e;
                          Ie(T, {
                            instancePath: t + "/preferredVersions/php",
                            parentData: m,
                            parentDataProperty: "php",
                            rootData: u
                          }) || (s = s === null ? Ie.errors : s.concat(
                            Ie.errors
                          ), e = s.length);
                          var _ = L === e;
                          if (R = R || _, !R) {
                            const U = e;
                            if (typeof T != "string") {
                              const I = {
                                instancePath: t + "/preferredVersions/php",
                                schemaPath: "#/properties/preferredVersions/properties/php/anyOf/1/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              };
                              s === null ? s = [
                                I
                              ] : s.push(
                                I
                              ), e++;
                            }
                            if (T !== "latest") {
                              const I = {
                                instancePath: t + "/preferredVersions/php",
                                schemaPath: "#/properties/preferredVersions/properties/php/anyOf/1/const",
                                keyword: "const",
                                params: {
                                  allowedValue: "latest"
                                },
                                message: "must be equal to constant"
                              };
                              s === null ? s = [
                                I
                              ] : s.push(
                                I
                              ), e++;
                            }
                            var _ = U === e;
                            R = R || _;
                          }
                          if (R)
                            e = w, s !== null && (w ? s.length = w : s = null);
                          else {
                            const U = {
                              instancePath: t + "/preferredVersions/php",
                              schemaPath: "#/properties/preferredVersions/properties/php/anyOf",
                              keyword: "anyOf",
                              params: {},
                              message: "must match a schema in anyOf"
                            };
                            return s === null ? s = [U] : s.push(U), e++, V.errors = s, !1;
                          }
                          var h = P === e;
                        } else
                          var h = !0;
                        if (h)
                          if (m.wp !== void 0) {
                            const T = e;
                            if (typeof m.wp != "string")
                              return V.errors = [
                                {
                                  instancePath: t + "/preferredVersions/wp",
                                  schemaPath: "#/properties/preferredVersions/properties/wp/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                }
                              ], !1;
                            var h = T === e;
                          } else
                            var h = !0;
                      }
                    }
                  } else
                    return V.errors = [
                      {
                        instancePath: t + "/preferredVersions",
                        schemaPath: "#/properties/preferredVersions/type",
                        keyword: "type",
                        params: { type: "object" },
                        message: "must be object"
                      }
                    ], !1;
                var p = N === e;
              } else
                var p = !0;
              if (p) {
                if (r.features !== void 0) {
                  let m = r.features;
                  const N = e;
                  if (e === N)
                    if (m && typeof m == "object" && !Array.isArray(m)) {
                      const b = e;
                      for (const O in m)
                        if (!(O === "intl" || O === "networking"))
                          return V.errors = [
                            {
                              instancePath: t + "/features",
                              schemaPath: "#/properties/features/additionalProperties",
                              keyword: "additionalProperties",
                              params: {
                                additionalProperty: O
                              },
                              message: "must NOT have additional properties"
                            }
                          ], !1;
                      if (b === e) {
                        if (m.intl !== void 0) {
                          const O = e;
                          if (typeof m.intl != "boolean")
                            return V.errors = [
                              {
                                instancePath: t + "/features/intl",
                                schemaPath: "#/properties/features/properties/intl/type",
                                keyword: "type",
                                params: {
                                  type: "boolean"
                                },
                                message: "must be boolean"
                              }
                            ], !1;
                          var k = O === e;
                        } else
                          var k = !0;
                        if (k)
                          if (m.networking !== void 0) {
                            const O = e;
                            if (typeof m.networking != "boolean")
                              return V.errors = [
                                {
                                  instancePath: t + "/features/networking",
                                  schemaPath: "#/properties/features/properties/networking/type",
                                  keyword: "type",
                                  params: {
                                    type: "boolean"
                                  },
                                  message: "must be boolean"
                                }
                              ], !1;
                            var k = O === e;
                          } else
                            var k = !0;
                      }
                    } else
                      return V.errors = [
                        {
                          instancePath: t + "/features",
                          schemaPath: "#/properties/features/type",
                          keyword: "type",
                          params: { type: "object" },
                          message: "must be object"
                        }
                      ], !1;
                  var p = N === e;
                } else
                  var p = !0;
                if (p) {
                  if (r.extraLibraries !== void 0) {
                    let m = r.extraLibraries;
                    const N = e;
                    if (e === N)
                      if (Array.isArray(m)) {
                        var E = !0;
                        const b = m.length;
                        for (let O = 0; O < b; O++) {
                          let T = m[O];
                          const P = e;
                          if (typeof T != "string")
                            return V.errors = [
                              {
                                instancePath: t + "/extraLibraries/" + O,
                                schemaPath: "#/definitions/ExtraLibrary/type",
                                keyword: "type",
                                params: {
                                  type: "string"
                                },
                                message: "must be string"
                              }
                            ], !1;
                          if (T !== "wp-cli")
                            return V.errors = [
                              {
                                instancePath: t + "/extraLibraries/" + O,
                                schemaPath: "#/definitions/ExtraLibrary/const",
                                keyword: "const",
                                params: {
                                  allowedValue: "wp-cli"
                                },
                                message: "must be equal to constant"
                              }
                            ], !1;
                          var E = P === e;
                          if (!E)
                            break;
                        }
                      } else
                        return V.errors = [
                          {
                            instancePath: t + "/extraLibraries",
                            schemaPath: "#/properties/extraLibraries/type",
                            keyword: "type",
                            params: {
                              type: "array"
                            },
                            message: "must be array"
                          }
                        ], !1;
                    var p = N === e;
                  } else
                    var p = !0;
                  if (p) {
                    if (r.constants !== void 0) {
                      let m = r.constants;
                      const N = e;
                      if (e === e)
                        if (m && typeof m == "object" && !Array.isArray(m))
                          for (const O in m) {
                            let T = m[O];
                            const P = e;
                            if (typeof T != "string" && typeof T != "boolean" && !(typeof T == "number" && isFinite(T)))
                              return V.errors = [
                                {
                                  instancePath: t + "/constants/" + O.replace(
                                    /~/g,
                                    "~0"
                                  ).replace(
                                    /\//g,
                                    "~1"
                                  ),
                                  schemaPath: "#/definitions/PHPConstants/additionalProperties/type",
                                  keyword: "type",
                                  params: {
                                    type: gs.additionalProperties.type
                                  },
                                  message: "must be string,boolean,number"
                                }
                              ], !1;
                            var $ = P === e;
                            if (!$)
                              break;
                          }
                        else
                          return V.errors = [
                            {
                              instancePath: t + "/constants",
                              schemaPath: "#/definitions/PHPConstants/type",
                              keyword: "type",
                              params: {
                                type: "object"
                              },
                              message: "must be object"
                            }
                          ], !1;
                      var p = N === e;
                    } else
                      var p = !0;
                    if (p) {
                      if (r.plugins !== void 0) {
                        let m = r.plugins;
                        const N = e;
                        if (e === N)
                          if (Array.isArray(m)) {
                            var g = !0;
                            const b = m.length;
                            for (let O = 0; O < b; O++) {
                              let T = m[O];
                              const P = e, w = e;
                              let R = !1;
                              const L = e;
                              if (typeof T != "string") {
                                const F = {
                                  instancePath: t + "/plugins/" + O,
                                  schemaPath: "#/properties/plugins/items/anyOf/0/type",
                                  keyword: "type",
                                  params: {
                                    type: "string"
                                  },
                                  message: "must be string"
                                };
                                s === null ? s = [
                                  F
                                ] : s.push(
                                  F
                                ), e++;
                              }
                              var B = L === e;
                              if (R = R || B, !R) {
                                const F = e;
                                Y(
                                  T,
                                  {
                                    instancePath: t + "/plugins/" + O,
                                    parentData: m,
                                    parentDataProperty: O,
                                    rootData: u
                                  }
                                ) || (s = s === null ? Y.errors : s.concat(
                                  Y.errors
                                ), e = s.length);
                                var B = F === e;
                                R = R || B;
                              }
                              if (R)
                                e = w, s !== null && (w ? s.length = w : s = null);
                              else {
                                const F = {
                                  instancePath: t + "/plugins/" + O,
                                  schemaPath: "#/properties/plugins/items/anyOf",
                                  keyword: "anyOf",
                                  params: {},
                                  message: "must match a schema in anyOf"
                                };
                                return s === null ? s = [
                                  F
                                ] : s.push(
                                  F
                                ), e++, V.errors = s, !1;
                              }
                              var g = P === e;
                              if (!g)
                                break;
                            }
                          } else
                            return V.errors = [
                              {
                                instancePath: t + "/plugins",
                                schemaPath: "#/properties/plugins/type",
                                keyword: "type",
                                params: {
                                  type: "array"
                                },
                                message: "must be array"
                              }
                            ], !1;
                        var p = N === e;
                      } else
                        var p = !0;
                      if (p) {
                        if (r.siteOptions !== void 0) {
                          let m = r.siteOptions;
                          const N = e;
                          if (e === N)
                            if (m && typeof m == "object" && !Array.isArray(
                              m
                            )) {
                              const b = e;
                              for (const O in m)
                                if (O !== "blogname") {
                                  const T = e;
                                  if (typeof m[O] != "string")
                                    return V.errors = [
                                      {
                                        instancePath: t + "/siteOptions/" + O.replace(
                                          /~/g,
                                          "~0"
                                        ).replace(
                                          /\//g,
                                          "~1"
                                        ),
                                        schemaPath: "#/properties/siteOptions/additionalProperties/type",
                                        keyword: "type",
                                        params: {
                                          type: "string"
                                        },
                                        message: "must be string"
                                      }
                                    ], !1;
                                  var X = T === e;
                                  if (!X)
                                    break;
                                }
                              if (b === e && m.blogname !== void 0 && typeof m.blogname != "string")
                                return V.errors = [
                                  {
                                    instancePath: t + "/siteOptions/blogname",
                                    schemaPath: "#/properties/siteOptions/properties/blogname/type",
                                    keyword: "type",
                                    params: {
                                      type: "string"
                                    },
                                    message: "must be string"
                                  }
                                ], !1;
                            } else
                              return V.errors = [
                                {
                                  instancePath: t + "/siteOptions",
                                  schemaPath: "#/properties/siteOptions/type",
                                  keyword: "type",
                                  params: {
                                    type: "object"
                                  },
                                  message: "must be object"
                                }
                              ], !1;
                          var p = N === e;
                        } else
                          var p = !0;
                        if (p) {
                          if (r.login !== void 0) {
                            let m = r.login;
                            const N = e, v = e;
                            let b = !1;
                            const O = e;
                            if (typeof m != "boolean") {
                              const P = {
                                instancePath: t + "/login",
                                schemaPath: "#/properties/login/anyOf/0/type",
                                keyword: "type",
                                params: {
                                  type: "boolean"
                                },
                                message: "must be boolean"
                              };
                              s === null ? s = [
                                P
                              ] : s.push(
                                P
                              ), e++;
                            }
                            var Z = O === e;
                            if (b = b || Z, !b) {
                              const P = e;
                              if (e === P)
                                if (m && typeof m == "object" && !Array.isArray(
                                  m
                                )) {
                                  let R;
                                  if (m.username === void 0 && (R = "username") || m.password === void 0 && (R = "password")) {
                                    const L = {
                                      instancePath: t + "/login",
                                      schemaPath: "#/properties/login/anyOf/1/required",
                                      keyword: "required",
                                      params: {
                                        missingProperty: R
                                      },
                                      message: "must have required property '" + R + "'"
                                    };
                                    s === null ? s = [
                                      L
                                    ] : s.push(
                                      L
                                    ), e++;
                                  } else {
                                    const L = e;
                                    for (const U in m)
                                      if (!(U === "username" || U === "password")) {
                                        const F = {
                                          instancePath: t + "/login",
                                          schemaPath: "#/properties/login/anyOf/1/additionalProperties",
                                          keyword: "additionalProperties",
                                          params: {
                                            additionalProperty: U
                                          },
                                          message: "must NOT have additional properties"
                                        };
                                        s === null ? s = [
                                          F
                                        ] : s.push(
                                          F
                                        ), e++;
                                        break;
                                      }
                                    if (L === e) {
                                      if (m.username !== void 0) {
                                        const U = e;
                                        if (typeof m.username != "string") {
                                          const F = {
                                            instancePath: t + "/login/username",
                                            schemaPath: "#/properties/login/anyOf/1/properties/username/type",
                                            keyword: "type",
                                            params: {
                                              type: "string"
                                            },
                                            message: "must be string"
                                          };
                                          s === null ? s = [
                                            F
                                          ] : s.push(
                                            F
                                          ), e++;
                                        }
                                        var q = U === e;
                                      } else
                                        var q = !0;
                                      if (q)
                                        if (m.password !== void 0) {
                                          const U = e;
                                          if (typeof m.password != "string") {
                                            const I = {
                                              instancePath: t + "/login/password",
                                              schemaPath: "#/properties/login/anyOf/1/properties/password/type",
                                              keyword: "type",
                                              params: {
                                                type: "string"
                                              },
                                              message: "must be string"
                                            };
                                            s === null ? s = [
                                              I
                                            ] : s.push(
                                              I
                                            ), e++;
                                          }
                                          var q = U === e;
                                        } else
                                          var q = !0;
                                    }
                                  }
                                } else {
                                  const R = {
                                    instancePath: t + "/login",
                                    schemaPath: "#/properties/login/anyOf/1/type",
                                    keyword: "type",
                                    params: {
                                      type: "object"
                                    },
                                    message: "must be object"
                                  };
                                  s === null ? s = [
                                    R
                                  ] : s.push(
                                    R
                                  ), e++;
                                }
                              var Z = P === e;
                              b = b || Z;
                            }
                            if (b)
                              e = v, s !== null && (v ? s.length = v : s = null);
                            else {
                              const P = {
                                instancePath: t + "/login",
                                schemaPath: "#/properties/login/anyOf",
                                keyword: "anyOf",
                                params: {},
                                message: "must match a schema in anyOf"
                              };
                              return s === null ? s = [
                                P
                              ] : s.push(
                                P
                              ), e++, V.errors = s, !1;
                            }
                            var p = N === e;
                          } else
                            var p = !0;
                          if (p) {
                            if (r.steps !== void 0) {
                              let m = r.steps;
                              const N = e;
                              if (e === N)
                                if (Array.isArray(
                                  m
                                )) {
                                  var K = !0;
                                  const b = m.length;
                                  for (let O = 0; O < b; O++) {
                                    let T = m[O];
                                    const P = e, w = e;
                                    let R = !1;
                                    const L = e;
                                    o(
                                      T,
                                      {
                                        instancePath: t + "/steps/" + O,
                                        parentData: m,
                                        parentDataProperty: O,
                                        rootData: u
                                      }
                                    ) || (s = s === null ? o.errors : s.concat(
                                      o.errors
                                    ), e = s.length);
                                    var W = L === e;
                                    if (R = R || W, !R) {
                                      const F = e;
                                      if (typeof T != "string") {
                                        const C = {
                                          instancePath: t + "/steps/" + O,
                                          schemaPath: "#/properties/steps/items/anyOf/1/type",
                                          keyword: "type",
                                          params: {
                                            type: "string"
                                          },
                                          message: "must be string"
                                        };
                                        s === null ? s = [
                                          C
                                        ] : s.push(
                                          C
                                        ), e++;
                                      }
                                      var W = F === e;
                                      if (R = R || W, !R) {
                                        const C = e, D = {
                                          instancePath: t + "/steps/" + O,
                                          schemaPath: "#/properties/steps/items/anyOf/2/not",
                                          keyword: "not",
                                          params: {},
                                          message: "must NOT be valid"
                                        };
                                        s === null ? s = [
                                          D
                                        ] : s.push(
                                          D
                                        ), e++;
                                        var W = C === e;
                                        if (R = R || W, !R) {
                                          const S = e;
                                          if (typeof T != "boolean") {
                                            const M = {
                                              instancePath: t + "/steps/" + O,
                                              schemaPath: "#/properties/steps/items/anyOf/3/type",
                                              keyword: "type",
                                              params: {
                                                type: "boolean"
                                              },
                                              message: "must be boolean"
                                            };
                                            s === null ? s = [
                                              M
                                            ] : s.push(
                                              M
                                            ), e++;
                                          }
                                          if (T !== !1) {
                                            const M = {
                                              instancePath: t + "/steps/" + O,
                                              schemaPath: "#/properties/steps/items/anyOf/3/const",
                                              keyword: "const",
                                              params: {
                                                allowedValue: !1
                                              },
                                              message: "must be equal to constant"
                                            };
                                            s === null ? s = [
                                              M
                                            ] : s.push(
                                              M
                                            ), e++;
                                          }
                                          var W = S === e;
                                          if (R = R || W, !R) {
                                            const M = e;
                                            if (T !== null) {
                                              const oe = {
                                                instancePath: t + "/steps/" + O,
                                                schemaPath: "#/properties/steps/items/anyOf/4/type",
                                                keyword: "type",
                                                params: {
                                                  type: "null"
                                                },
                                                message: "must be null"
                                              };
                                              s === null ? s = [
                                                oe
                                              ] : s.push(
                                                oe
                                              ), e++;
                                            }
                                            var W = M === e;
                                            R = R || W;
                                          }
                                        }
                                      }
                                    }
                                    if (R)
                                      e = w, s !== null && (w ? s.length = w : s = null);
                                    else {
                                      const F = {
                                        instancePath: t + "/steps/" + O,
                                        schemaPath: "#/properties/steps/items/anyOf",
                                        keyword: "anyOf",
                                        params: {},
                                        message: "must match a schema in anyOf"
                                      };
                                      return s === null ? s = [
                                        F
                                      ] : s.push(
                                        F
                                      ), e++, V.errors = s, !1;
                                    }
                                    var K = P === e;
                                    if (!K)
                                      break;
                                  }
                                } else
                                  return V.errors = [
                                    {
                                      instancePath: t + "/steps",
                                      schemaPath: "#/properties/steps/type",
                                      keyword: "type",
                                      params: {
                                        type: "array"
                                      },
                                      message: "must be array"
                                    }
                                  ], !1;
                              var p = N === e;
                            } else
                              var p = !0;
                            if (p)
                              if (r.$schema !== void 0) {
                                const m = e;
                                if (typeof r.$schema != "string")
                                  return V.errors = [
                                    {
                                      instancePath: t + "/$schema",
                                      schemaPath: "#/properties/%24schema/type",
                                      keyword: "type",
                                      params: {
                                        type: "string"
                                      },
                                      message: "must be string"
                                    }
                                  ], !1;
                                var p = m === e;
                              } else
                                var p = !0;
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    } else
      return V.errors = [
        {
          instancePath: t,
          schemaPath: "#/type",
          keyword: "type",
          params: { type: "object" },
          message: "must be object"
        }
      ], !1;
  return V.errors = s, e === 0;
}
function sr(r, { instancePath: t = "", parentData: n, parentDataProperty: l, rootData: u = r } = {}) {
  let s = null, e = 0;
  return V(r, {
    instancePath: t,
    parentData: n,
    parentDataProperty: l,
    rootData: u
  }) || (s = s === null ? V.errors : s.concat(V.errors), e = s.length), sr.errors = s, e === 0;
}
const { wpCLI: _s, ...$r } = ys, ks = {
  ...$r,
  "wp-cli": _s,
  importFile: $r.importWxr
};
class Os extends Error {
  constructor(t, n) {
    super(t), this.name = "InvalidBlueprintError", this.validationErrors = n;
  }
}
class Ts extends Error {
  constructor(t) {
    const { stepNumber: n, step: l, cause: u } = t, s = u instanceof Error ? u : new Error(String(u)), e = `Error when executing the blueprint step #${n}`, p = s.message ? `${e}: ${s.message}` : e;
    super(p, { cause: s }), this.name = "BlueprintStepExecutionError", this.stepNumber = n, this.step = l, this.messages = (s.message || "").split(`
`).map((d) => d.trim()).filter(Boolean);
  }
}
async function Es(r, t = {}) {
  const n = {
    ...t
  };
  let l;
  return Vr(r) ? (l = await $s(r), n.streamBundledFile = function(...u) {
    return r.read(...u);
  }) : l = r, js(l, n);
}
function Vr(r) {
  return r && "read" in r && typeof r.read == "function";
}
async function $s(r) {
  if (!Vr(r))
    return r;
  const n = await (await r.read("blueprint.json")).text();
  return JSON.parse(n);
}
function js(r, {
  progress: t = new Yr(),
  semaphore: n = new qr({ concurrency: 3 }),
  onStepCompleted: l = () => {
  },
  onBlueprintValidated: u = () => {
  },
  corsProxy: s,
  streamBundledFile: e,
  gitAdditionalHeadersCallback: p,
  additionalSteps: d
} = {}) {
  var g, B, X, Z, q, K, W, re, m, N;
  r = structuredClone(r), r = {
    ...r,
    steps: (r.steps || []).filter(Ss).filter(Ls)
  }, r.steps = [...r.steps || [], ...d || []];
  for (const v of r.steps)
    !v || typeof v != "object" || (v.step === "importFile" ? (v.step = "importWxr", ae.warn(
      'The "importFile" step is deprecated. Use "importWxr" instead.'
    )) : (v == null ? void 0 : v.step) === "installPlugin" && "pluginZipFile" in v ? (v.pluginData = v.pluginZipFile, ae.warn(
      'The "pluginZipFile" option of the "installPlugin" step is deprecated. Use "pluginData" instead.'
    )) : (v == null ? void 0 : v.step) === "installTheme" && "themeZipFile" in v && (v.themeData = v.themeZipFile, ae.warn(
      'The "themeZipFile" option of the "installTheme" step is deprecated. Use "themeData" instead.'
    )));
  if (r.constants && r.steps.unshift({
    step: "defineWpConfigConsts",
    consts: r.constants
  }), r.siteOptions && r.steps.unshift({
    step: "setSiteOptions",
    options: r.siteOptions
  }), r.plugins) {
    const v = r.plugins.map((b) => typeof b == "string" ? xs(b) ? {
      resource: "zip",
      inner: {
        resource: "git:directory",
        url: b.replace(/\.git\/?$/, "").replace(/\/$/, ""),
        ref: "HEAD"
      }
    } : b.startsWith("https://") ? {
      resource: "url",
      url: b
    } : {
      resource: "wordpress.org/plugins",
      slug: b
    } : b).map((b) => ({
      step: "installPlugin",
      pluginData: b
    }));
    r.steps.unshift(...v);
  }
  r.login && r.steps.unshift({
    step: "login",
    ...r.login === !0 ? { username: "admin" } : r.login
  });
  const y = ((g = r.steps) == null ? void 0 : g.findIndex(
    (v) => typeof v == "object" && (v == null ? void 0 : v.step) && ["wp-cli", "enableMultisite"].includes(v.step)
  )) ?? -1;
  if ((B = r == null ? void 0 : r.extraLibraries) != null && B.includes("wp-cli") || y !== -1) {
    const v = {
      step: "writeFile",
      data: Vt,
      path: wr
    };
    y === -1 ? (X = r.steps) == null || X.push(v) : (Z = r.steps) == null || Z.splice(
      y,
      0,
      v
    );
  }
  const _ = (q = r.steps) == null ? void 0 : q.findIndex(
    (v) => typeof v == "object" && (v == null ? void 0 : v.step) === "importWxr"
  );
  _ !== void 0 && _ > -1 && ((K = r.steps) == null || K.splice(_, 0, {
    step: "installPlugin",
    pluginData: {
      resource: "wordpress.org/plugins",
      slug: "wordpress-importer"
    }
  }));
  const h = As(r);
  if (!h.valid) {
    const { errors: v } = h, b = qs(r, v);
    throw new Os(
      `Invalid Blueprint: The Blueprint does not conform to the schema.

Found ${v.length} validation error(s):

${b}

Please review your Blueprint and fix these issues. Learn more about the Blueprint format: https://wordpress.github.io/wordpress-playground/blueprints/data-format`,
      v
    );
  }
  u(r);
  const k = r.steps || [], E = k.reduce(
    (v, b) => {
      var O;
      return v + (((O = b.progress) == null ? void 0 : O.weight) || 1);
    },
    0
  ), $ = k.map(
    (v) => Fs(v, {
      semaphore: n,
      rootProgressTracker: t,
      totalProgressWeight: E,
      corsProxy: s,
      streamBundledFile: e,
      gitAdditionalHeadersCallback: p
    })
  );
  return {
    versions: {
      php: Rs(
        (W = r.preferredVersions) == null ? void 0 : W.php,
        Xr,
        Jr
      ),
      wp: ((re = r.preferredVersions) == null ? void 0 : re.wp) || "latest"
    },
    features: {
      // Disable intl by default to reduce the transfer size
      intl: ((m = r.features) == null ? void 0 : m.intl) ?? !1,
      // Enable network access by default
      networking: ((N = r.features) == null ? void 0 : N.networking) ?? !0
    },
    extraLibraries: r.extraLibraries || [],
    run: async (v) => {
      try {
        for (const { resources: b } of $)
          for (const O of b)
            O.setPlayground(v), O.isAsync && O.resolve().catch(() => {
            });
        for (const [b, { run: O, step: T }] of Object.entries($))
          try {
            const P = await O(v);
            l(P, T);
          } catch (P) {
            const w = Number(b) + 1;
            throw new Ts({
              stepNumber: w,
              step: T,
              cause: P
            });
          }
      } finally {
        try {
          const b = await v.pathToInternalUrl(r.landingPage || "/");
          await v.goTo(
            "/index.php?playground-redirection-handler&next=" + encodeURIComponent(b)
          );
        } catch {
        }
        t.finish();
      }
    }
  };
}
function qs(r, t) {
  return t.map((n, l) => {
    var p;
    const u = n.instancePath || "/";
    let s = n.message || "validation failed", e = "";
    if (s.includes("must NOT have additional properties")) {
      const d = (p = n.params) == null ? void 0 : p.additionalProperty;
      if (d) {
        s = `has unexpected property "${d}"`;
        try {
          const y = u.split("/").filter(Boolean);
          let _ = r;
          for (const h of y)
            _ && typeof _ == "object" && (_ = _[h]);
          if (_ && typeof _ == "object") {
            const h = _[d], k = JSON.stringify(h);
            e = `
  "${d}": ${k}
  ${"^".repeat(
              d.length + 2
            )} This property is not recognized`;
          }
        } catch {
        }
      }
    } else
      try {
        const d = u.split("/").filter(Boolean);
        let y = r;
        for (const _ of d)
          y && typeof y == "object" && (y = y[_]);
        if (y !== void 0) {
          const _ = JSON.stringify(y, null, 2);
          e = `
  Value: ${_.length > 200 ? _.substring(0, 200) + "..." : _}`;
        }
      } catch {
      }
    return `${l + 1}. At path "${u}": ${s}${e}`;
  }).join(`

`);
}
function As(r) {
  var u;
  const t = sr(r);
  if (t)
    return { valid: t };
  const n = /* @__PURE__ */ new Set();
  for (const s of sr.errors)
    s.schemaPath.startsWith("#/properties/steps/items/anyOf") || n.add(s.instancePath);
  return {
    valid: !1,
    errors: ((u = sr.errors) == null ? void 0 : u.filter(
      (s) => !(s.schemaPath.startsWith(
        "#/properties/steps/items/anyOf"
      ) && n.has(s.instancePath))
    )) ?? []
  };
}
function Rs(r, t, n) {
  return (r === "7.2" || r === "7.3") && (ae.warn(
    `PHP ${r} is no longer supported. Automatically upgrading to PHP 7.4.`
  ), r = "7.4"), r && t.includes(r) ? r : n;
}
function Ss(r) {
  return !!(typeof r == "object" && r);
}
function Ls(r) {
  return ["setPhpIniEntry", "request"].includes(r.step) ? (ae.warn(
    `The "${r.step}" Blueprint is no longer supported and you can remove it from your Blueprint.`
  ), !1) : !0;
}
function Fs(r, {
  semaphore: t,
  rootProgressTracker: n,
  totalProgressWeight: l,
  corsProxy: u,
  streamBundledFile: s,
  gitAdditionalHeadersCallback: e
}) {
  var E;
  const p = n.stage(
    (((E = r.progress) == null ? void 0 : E.weight) || 1) / l
  ), d = {};
  for (const $ of Object.keys(r)) {
    let g = r[$];
    vt(g) && (g = we.create(g, {
      semaphore: t,
      corsProxy: u,
      streamBundledFile: s,
      gitAdditionalHeadersCallback: e
    })), d[$] = g;
  }
  const y = async ($) => {
    var g;
    try {
      return p.fillSlowly(), await ks[r.step](
        $,
        await Ns(d),
        {
          tracker: p,
          initialCaption: (g = r.progress) == null ? void 0 : g.caption
        }
      );
    } finally {
      p.finish();
    }
  }, _ = jr(d), h = jr(d).filter(
    ($) => $.isAsync
  ), k = 1 / (h.length + 1);
  for (const $ of h)
    $.progress = p.stage(k);
  return { run: y, step: r, resources: _ };
}
function jr(r) {
  const t = [];
  for (const n in r) {
    const l = r[n];
    l instanceof we && t.push(l);
  }
  return t;
}
async function Ns(r) {
  const t = {};
  for (const n in r) {
    const l = r[n];
    l instanceof we ? t[n] = await l.resolve() : t[n] = l;
  }
  return t;
}
async function Ks(r, t) {
  await r.run(t);
}
function xs(r) {
  return !!(/^https:\/\/.+\.git\/?$/.test(r) || /^https:\/\/github\.com\/[^/]+\/[^/]+\/?$/.test(r) || /^https:\/\/gitlab\.com\/[^/]+\/[^/]+(\/[^/]+)*\/?$/.test(r));
}
async function Cs() {
  const r = (
    // @ts-ignore
    (await import("./blueprints-pMn3V9MZ.js")).default
  );
  return new File([r], "blueprints.phar", {
    type: "application/zip"
  });
}
function Ds(r) {
  if (typeof r == "object" && "type" in r && ["inline-file", "file-reference"].includes(r.type))
    return r;
  if (!r)
    return {
      type: "inline-file",
      contents: "{}"
    };
  if (typeof r != "string")
    return {
      type: "inline-file",
      contents: JSON.stringify(r)
    };
  try {
    return JSON.parse(r), {
      type: "inline-file",
      contents: r
    };
  } catch {
    return {
      type: "file-reference",
      reference: r
    };
  }
}
async function ei(r) {
  var _, h;
  const t = r.cliArgs || [];
  for (const k of t)
    if (k.startsWith("--site-path="))
      throw new Error(
        "The --site-path CLI argument must not be provided. In Playground, it is always set to /wordpress."
      );
  t.push("--site-path=/wordpress"), t.find((k) => k.startsWith("--db-engine=")) || t.push("--db-engine=sqlite");
  const l = r.php, u = (r == null ? void 0 : r.onMessage) || (() => {
  }), s = await Cs();
  l.writeFile(
    "/tmp/blueprints.phar",
    new Uint8Array(await s.arrayBuffer())
  );
  const e = Ds(
    r.blueprint
  );
  let p = "";
  switch (e.type) {
    case "inline-file":
      l.writeFile(
        "/tmp/blueprint.json",
        e.contents
      ), p = "/tmp/blueprint.json";
      break;
    case "file-reference":
      p = e.reference;
      break;
  }
  const d = await l.onMessage(async (k) => {
    try {
      const E = typeof k == "string" ? JSON.parse(k) : k;
      if (!E)
        return;
      await new Promise(($) => setTimeout($, 0)), E.type.startsWith("blueprint.") && await u(E);
    } catch (E) {
      ae.warn("Failed to parse message as JSON:", k, E);
    }
  });
  await (l == null ? void 0 : l.writeFile(
    "/tmp/run-blueprints.php",
    `<?php
function playground_http_client_factory() {
	return new WordPress\\HttpClient\\Client([
		// sockets transport is somehow faster than curl in Playground. Maybe
		// it uses a larger chunk size?
		'transport' => 'sockets',
	]);
}
playground_add_filter('blueprint.http_client', 'playground_http_client_factory');

function playground_on_blueprint_target_resolved() {
	post_message_to_js(json_encode([
		'type' => 'blueprint.target_resolved',
	]));
}
playground_add_filter('blueprint.target_resolved', 'playground_on_blueprint_target_resolved');

playground_add_filter('blueprint.resolved', 'playground_on_blueprint_resolved');
function playground_on_blueprint_resolved($blueprint) {
	$additional_blueprint_steps = json_decode(${be(
      JSON.stringify(((_ = r.blueprintOverrides) == null ? void 0 : _.additionalSteps) || [])
    )}, true);
	if(count($additional_blueprint_steps) > 0) {
		$blueprint['additionalStepsAfterExecution'] = array_merge(
			$blueprint['additionalStepsAfterExecution'] ?? [],
			$additional_blueprint_steps
		);
	}

	$wp_version_override = json_decode(${be(
      JSON.stringify(((h = r.blueprintOverrides) == null ? void 0 : h.wordpressVersion) || null)
    )}, true);
	if($wp_version_override) {
		$blueprint['wordpressVersion'] = $wp_version_override;
	}
	return $blueprint;
}

function playground_progress_reporter() {
	class PlaygroundProgressReporter implements ProgressReporter {

		public function reportProgress(float $progress, string $caption): void {
			$this->writeJsonMessage([
				'type' => 'blueprint.progress',
				'progress' => round($progress, 2),
				'caption' => $caption
			]);
		}

		public function reportError(string $message, ?Throwable $exception = null): void {
			$errorData = [
				'type' => 'blueprint.error',
				'message' => $message
			];

			if ($exception) {
				$errorData['details'] = [
					'exception' => get_class($exception),
					'message' => $exception->getMessage(),
					'file' => $exception->getFile(),
					'line' => $exception->getLine(),
					'trace' => $exception->getTraceAsString()
				];
			}

			$this->writeJsonMessage($errorData);
		}

		public function reportCompletion(string $message): void {
			$this->writeJsonMessage([
				'type' => 'blueprint.completion',
				'message' => $message
			]);
		}

		public function close(): void {}

		private function writeJsonMessage(array $data): void {
			post_message_to_js(json_encode($data));
		}
	}
	return new PlaygroundProgressReporter();
}
playground_add_filter('blueprint.progress_reporter', 'playground_progress_reporter');
require( "/tmp/blueprints.phar" );
`
  ));
  const y = await l.cli([
    "/internal/shared/bin/php",
    "/tmp/run-blueprints.php",
    "exec",
    p,
    ...t
  ]);
  return y.finished.finally(d), y;
}
class Us extends Error {
  constructor(t, n, l) {
    super(t, l), this.name = "BlueprintFetchError", this.url = n;
  }
}
async function ri(r) {
  let t;
  try {
    const n = await fetch(r, {
      credentials: "omit"
    });
    if (!n.ok)
      throw new Error(`Failed to fetch blueprint from ${r}`);
    t = await n.arrayBuffer();
  } catch (n) {
    throw new Us(
      `Blueprint file could not be resolved from ${r}: ${n instanceof Error ? n.message : String(n)}`,
      r,
      { cause: n }
    );
  }
  try {
    const n = new TextDecoder().decode(t);
    return JSON.parse(n), new it([
      new ot({
        "blueprint.json": n
      }),
      new at({
        baseUrl: r
      })
    ]);
  } catch (n) {
    if (await Bs(t))
      return Ws(t);
    throw new Error(
      `Blueprint file at ${r} is neither a valid JSON nor a ZIP file.`,
      { cause: n }
    );
  }
}
function Is(r) {
  const t = r.map((l) => Gr(l));
  if (t.some((l) => kr(l) === "blueprint.json" && yr(l) === ""))
    return "blueprint.json";
  const n = /* @__PURE__ */ new Set();
  for (const l of t) {
    const u = l.split("/")[0];
    u && u !== kr(l) && u !== "__MACOSX" && n.add(u);
  }
  if (n.size > 1)
    throw new Error(
      "ZIP contains multiple top-level directories. Bundle ZIPs must contain blueprint.json at the root or inside a single top-level directory."
    );
  if (n.size === 1) {
    const u = `${[...n][0]}/blueprint.json`;
    if (t.includes(u))
      return u;
  }
  throw new Error(
    "ZIP does not contain a blueprint.json. Place blueprint.json at the ZIP root or inside a single top-level directory."
  );
}
async function Ws(r) {
  const t = nt.fromArrayBuffer(r), n = await t.getAllFilePaths(), l = Is(n), u = yr(l);
  return u === "" ? t : new pt(u, t);
}
async function Bs(r) {
  if (r.byteLength < 4)
    return !1;
  const t = new Uint8Array(r, 0, 4);
  return t[0] === 80 && t[1] === 75 && t[2] === 3 && t[3] === 4;
}
async function ti(r) {
  if ((await or.create(r)).getVersion() === 1) {
    const n = await Es(
      r
    );
    return {
      wpVersion: n.versions.wp,
      phpVersion: n.versions.php,
      intl: n.features.intl,
      networking: n.features.networking,
      extraLibraries: n.extraLibraries,
      /*
       * Constants don't matter so much for temporary sites so let's
       * use an empty object here. We can't easily figure out which
       * additional constants were applied via playground.defineConstant()
       * at this stage anyway.
       *
       * This property is only relevant for stored sites to ensure they're
       * consistently applied across page reloads.
       */
      constants: {}
    };
  } else
    return {
      phpVersion: yt,
      wpVersion: "latest",
      intl: !1,
      networking: !0,
      constants: {},
      extraLibraries: []
    };
}
function si() {
}
export {
  Us as BlueprintFetchError,
  bt as BlueprintFilesystemRequiredError,
  or as BlueprintReflection,
  Ts as BlueprintStepExecutionError,
  Os as InvalidBlueprintError,
  fr as ResourceDownloadError,
  Fr as activatePlugin,
  Nr as activateTheme,
  Es as compileBlueprint,
  Es as compileBlueprintV1,
  Qt as cp,
  Ur as defineSiteUrl,
  br as defineWpConfigConsts,
  Zt as enableMultisite,
  is as exportWXR,
  $s as getBlueprintDeclaration,
  Cs as getV2Runner,
  Ir as importThemeStarterContent,
  rs as importWordPressFiles,
  Kt as importWxr,
  os as installPlugin,
  as as installTheme,
  Vr as isBlueprintBundle,
  Ss as isStepDefinition,
  ns as login,
  Gt as mkdir,
  Yt as mv,
  Bt as request,
  ps as resetData,
  ri as resolveRemoteBlueprint,
  ti as resolveRuntimeConfiguration,
  cr as rm,
  Jt as rmdir,
  Ks as runBlueprintSteps,
  Ks as runBlueprintV1Steps,
  ei as runBlueprintV2,
  Dt as runPHP,
  Ut as runPHPWithOptions,
  Wt as runSql,
  ls as runWpInstallationWizard,
  si as setPluginProxyURL,
  ms as setSiteLanguage,
  xr as setSiteOptions,
  Pr as unzip,
  zt as updateUserMeta,
  As as validateBlueprint,
  Dr as wpCLI,
  Wr as wpContentFilesExcludedFromExport,
  vr as writeFile,
  Xt as writeFiles,
  fs as zipWpContent
};
//# sourceMappingURL=index.js.map
