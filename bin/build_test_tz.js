import * as esbuild from 'esbuild'

await esbuild.build({
  entryPoints: ['test_tz.mjs'],
  bundle: true,
  platform: 'node',
  outfile: 'test_tz_bundled.cjs',
  target: ['es2020'],
  format: 'cjs',
})
