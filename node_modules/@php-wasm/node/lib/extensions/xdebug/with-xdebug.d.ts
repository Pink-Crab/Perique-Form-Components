import { type EmscriptenOptions } from '@php-wasm/universal';
export interface PathMapping {
    hostPath: string;
    vfsPath: string;
}
export interface XdebugOptions {
    ideKey?: string;
    pathMappings?: PathMapping[];
    pathSkippings?: string[];
}
export declare function withXdebug(version: "8.5" | "8.4" | "8.3" | "8.2" | "8.1" | "8.0" | "7.4" | undefined, options: EmscriptenOptions, xdebugOptions: XdebugOptions): Promise<EmscriptenOptions>;
