import * as esbuild from 'esbuild'

await esbuild.build({
  entryPoints: ['resources/js/filament-ethiopic-calendar.js'],
  bundle: true,
  minify: true,
  outfile: 'resources/dist/components/filament-ethiopic-calendar.js',
  target: ['es2020'],
  format: 'esm',
})
