import * as esbuild from 'esbuild'

await esbuild.build({
  entryPoints: ['compare_php_js.mjs'],
  bundle: true,
  platform: 'node',
  outfile: 'compare_php_js_bundled.cjs',
  target: ['es2020'],
  format: 'cjs',
})
