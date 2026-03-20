<?php

namespace Modules\Sermons\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Sermons\App\Models\Sermon;
use Modules\Sermons\App\Models\SermonBibleReference;
use Modules\Sermons\App\Models\SermonCategory;
use Modules\Sermons\App\Models\SermonExegesis;
use Modules\Sermons\App\Models\SermonOutline;
use Modules\Sermons\App\Models\SermonSeries;
use Modules\Sermons\App\Models\SermonTag;

class SermonsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        // Limpeza preventiva para evitar conflitos de slug com registros arquivados (SoftDelete)
        $categorySlug = 'teologia-expositiva';
        SermonCategory::withTrashed()->where('slug', $categorySlug)->forceDelete();

        // 1. Categoria Premium
        $category = SermonCategory::create([
            'slug' => $categorySlug,
            'name' => 'Teologia Expositiva',
            'description' => 'Sermões focados na exposição versículo a versículo, com rigor gramatical e histórico.',
            'is_active' => true,
            'order' => 1
        ]);

        $seriesSlug = 'efesios-a-supremacia-de-cristo';
        SermonSeries::withTrashed()->where('slug', $seriesSlug)->forceDelete();

        // 2. Série Premium
        $series = SermonSeries::create([
            'slug' => $seriesSlug,
            'user_id' => $user->id,
            'title' => 'Efésios: A Supremacia de Cristo na Igreja',
            'description' => 'Série completa de estudos profundos na Epístola aos Efésios, focando na obra redentora e no corpo de Cristo.',
            'status' => 'published',
            'is_featured' => true
        ]);

        // 3. Tags Estruturadas - Limpeza e recriação
        $tags = [
            'graca-soberana' => 'Graça Soberana',
            'soteriologia-biblica' => 'Soteriologia Bíblica',
            'estudo-do-grego' => 'Estudo do Grego',
            'depravacao-total' => 'Depravação Total'
        ];

        $tagIds = [];
        foreach ($tags as $slug => $name) {
            SermonTag::withTrashed()->where('slug', $slug)->forceDelete();
            $tag = SermonTag::create(['slug' => $slug, 'name' => $name]);
            $tagIds[] = $tag->id;
        }

        // 4. Conteúdo Profundo do Sermão
        $sermonTitle = 'Da Morte para a Vida: Uma Exposição Exegética do Milagre da Regeneração';
        $sermonSlug = Str::slug($sermonTitle);

        // IMPORTANTE: Limpar sermão existente (mesmo que deletado via softdelete)
        Sermon::withTrashed()->where('slug', $sermonSlug)->forceDelete();

        $introduction = '
<div class="prose dark:prose-invert max-w-none">
    <p class="lead text-lg font-semibold text-blue-600 dark:text-blue-400">Efésios 2:1-10 representa o "Monte Everest" da soteriologia paulina.</p>
    <p>Nesta passagem, o Apóstolo Paulo não apenas apresenta o Evangelho; ele disseca a condição humana caída sob a luz da santidade divina e, em seguida, reconstrói a nossa esperança sobre o fundamento inabalável da <em>Sola Gratia</em>. Este sermão não é uma simples exortação moral; é uma exposição sobre a ressurreição espiritual operada pelo Espírito Santo.</p>
    <p><strong>Contexto Histórico:</strong> Em Éfeso, uma metrópole dominada pelo ocultismo e pelo culto a Ártemis, Paulo escreve para lembrar aos crentes que a sua nova identidade não vem de mistérios pagãos, mas do mistério da vontade de Deus revelado em Cristo.</p>
</div>';

        $bodyOutline = '
<div class="space-y-8">
    <section>
        <h3 class="text-xl font-bold border-b-2 border-red-500 pb-2 mb-4">I. O Diagnóstico Somatológico: A Morte Espiritual (vv. 1-3)</h3>
        <ul class="list-disc pl-6 space-y-3">
            <li><strong>A Natureza do Estado (v. 1):</strong> Mortos em "delitos" (<em>paraptoma</em> - um desvio do caminho) e "pecados" (<em>hamartia</em> - errar o alvo).
                <p class="text-sm text-gray-500 italic mt-1 ml-4">Nota Exegética: O termo grego "nekros" implica incapacidade absoluta. Um morto não busca a Deus; ele precisa de uma ressurreição, não de terapia.</p>
            </li>
            <li><strong>A Influência Tríplice:</strong> O Mundo (sistema corrupto), a Carne (desejos distorcidos) e o Diabo (o príncipe da potestade do ar).</li>
            <li><strong>Filhos da Ira:</strong> Uma condição ontológica, não apenas comportamental.</li>
        </ul>
    </section>

    <section>
        <h3 class="text-xl font-bold border-b-2 border-green-500 pb-2 mb-4">II. A Reviravolta Divina: O Grande "Mas Deus" (vv. 4-7)</h3>
        <ul class="list-disc pl-6 space-y-3">
            <li><strong>A Fonte: Misericórdia e Amor (v. 4):</strong> Contrastando com a ira imerecida, temos a misericórdia (<em>eleos</em>) abundante e o amor (<em>agape</em>) incondicional.</li>
            <li><strong>A Ação Sinergética Federativa (vv. 5-6):</strong> Três verbos cruciais prefixados com <em>"syn"</em> (junto com):
                <ol class="list-decimal pl-6 mt-2">
                    <li><em>Synezoopoiesen</em>: Vivificou-nos juntamente com Cristo.</li>
                    <li><em>Synegeiren</em>: Ressuscitou-nos juntamente com Ele.</li>
                    <li><em>Synekathisen</em>: Assentou-nos nas regiões celestiais.</li>
                </ol>
            </li>
            <li><strong>O Propósito Eterno (v. 7):</strong> Exibir as "abundantes riquezas da Sua graça" pelos séculos dos séculos. No céu, seremos troféus da graça de Deus.</li>
        </ul>
    </section>

    <section>
        <h3 class="text-xl font-bold border-b-2 border-blue-500 pb-2 mb-4">III. O Mecanismo da Libertação: Fé e Graça (vv. 8-9)</h3>
        <ul class="list-disc pl-6 space-y-3">
            <li><strong>A Estrutura Gramatical:</strong> "Pela graça sois salvos, por meio da fé". A graça é a causa eficiente; a fé é o canal ou causa instrumental.</li>
            <li><strong>O Pronome Neutro (v. 8b):</strong> "E isto não vem de vós; é dom de Deus". No grego, <em>"touto"</em> (neutro) refere-se a todo o processo de salvação pela fé, não apenas à fé individualmente. Tudo é dom!</li>
            <li><strong>A Exclusão do Orgulho (v. 9):</strong> "Não de obras, para que ninguém se glorie". A salvação é projetada para humilhar o homem e exaltar a Cristo.</li>
        </ul>
    </section>

    <section>
        <h3 class="text-xl font-bold border-b-2 border-purple-500 pb-2 mb-4">IV. O Resultado Prático: O Poema de Deus (v. 10)</h3>
        <ul class="list-disc pl-6 space-y-3">
            <li><strong>A Obra de Arte:</strong> Fomos criados para ser o <em>"Poiema"</em> de Deus. Somos Sua feitura, Sua composição.</li>
            <li><strong>O Destino Pré-Ordenado:</strong> Criados para "boas obras, as quais Deus preparou de antemão". As obras são o FRUTO, não a RAIZ da árvore da salvação.</li>
        </ul>
    </section>
</div>';

        $fullContent = '
<article class="prose prose-slate dark:prose-invert lg:prose-lg mx-auto">
    <header>
        <h1>Exposição Exegética: A Transição da Morte para a Vida</h1>
        <p class="text-gray-500">Por: Pr. ' . $user->name . '</p>
    </header>

    <section>
        <h2>A Antropologia Paulina</h2>
        <p>Paulo começa com um choque de realidade. O homem não está apenas "perdido" ou "doente"; ele está <strong>nekros</strong> (morto). Espiritualmente falando, o ser humano sem Cristo é um cadáver ambulante, habitado por desejos que o levam cada vez mais longe do Criador. Esta é a doutrina da Depravação Total, um pilar fundamental da fé batista histórica.</p>
        <blockquote>"Andaste no caminho deste mundo, sob o domínio do príncipe da potestade do ar."</blockquote>
    </section>

    <section>
        <h2>O Grande Contraste: O Caráter de Deus</h2>
        <p>O versículo 4 contém a frase mais gloriosa de toda a Bíblia: <strong>"Mas Deus"</strong>. Se Deus não interviesse, estaríamos irremediavelmente perdidos. Contudo, Ele é <em>"plousios en eleei"</em> (rico em misericórdia). A misericórdia de Deus não é apenas um sentimento, é uma ação redentora que rompe os grilhões da morte espiritual.</p>
        <p>A salvação é operada <em>en Christo</em>. Tudo o que aconteceu com Cristo (Morte, Ressurreição, Ascensão) é aplicado ao crente legal e espiritualmente. Quando Ele ressuscitou, nós ressuscitamos com Ele. Nossa certidão de nascimento espiritual tem a data da ressurreição de Cristo.</p>
    </section>

    <section>
        <h2>Gracia Sola: A Base da Nossa Certeza</h2>
        <p>Nos versículos 8 e 9, Paulo constrói a fortaleza da nossa segurança. Se a salvação dependesse em 1% das nossas obras, nunca teríamos paz. Mas o apóstolo diz: <em>"Chariti este sesosmenoi"</em> (Pela graça tendes sido salvos). O tempo verbal (particípio perfeito passivo) indica uma ação concluída no passado com resultados permanentes no presente. Você está salvo!</p>
    </section>

    <section>
        <h2>Conclusão e Apelo</h2>
        <p>Você é o poema de Deus. Se hoje você sente o pulsar da vida espiritual, se você ama a Deus e deseja servi-Lo, entenda que isso é obra dEle. A sua vida é agora um palco onde a abundantes riquezas da graça de Deus estão sendo manifestadas. Viva à altura do seu Criador.</p>
    </section>
</article>';

        $sermon = Sermon::create([
            'slug' => $sermonSlug,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'sermon_series_id' => $series->id,
            'title' => $sermonTitle,
            'theme' => 'Regeneração, Soteriologia e Exegese de Efésios 2',
            'subtitle' => 'Uma Jornada pelo Coração do Evangelho',
            'description' => 'Exposição acadêmica e ministerial completa sobre a transição da morte espiritual para a vida em Cristo, focando na soberania de Deus e no grego bíblico.',
            'biblical_text_base' => 'Efésios 2:1-10',
            'central_proposition' => 'A salvação é uma interrupção soberana de Deus na morte humana, operada unicamente pela graça para produzir uma nova criatura que reflete a glória do Criador.',
            'historical_context' => 'Escrita pelo Apóstolo Paulo por volta de 60-62 d.C., durante seu primeiro aprisionamento em Roma. A carta aos Efésios não responde a erros específicos, mas visa fortalecer a identidade dos crentes em uma cultura pagã dominada pelo ocultismo efésio.',
            'introduction' => $introduction,
            'body_outline' => $bodyOutline,
            'practical_application' => '
<ol class="space-y-4">
    <li><strong>Reconhecimento da Humildade:</strong> Entenda que não há espaço para orgulho espiritual. Tudo o que temos de bom é emprestado de Deus.</li>
    <li><strong>Segurança na Graça:</strong> Pare de olhar para o seu desempenho para ganhar a aprovação de Deus; olhe para o desempenho de Cristo na cruz.</li>
    <li><strong>Ativismo da Graça:</strong> Desenvolva as "boas obras" preparadas por Deus, entendendo que elas são a linguagem de amor de um filho resgatado.</li>
    <li><strong>Estudo Radical:</strong> Utilize as ferramentas de interlinear e forte/grego para meditar nas profundezas de cada palavra inspirada por Deus.</li>
</ol>',
            'conclusion' => '<div class="bg-blue-50 dark:bg-blue-900/30 p-6 rounded-xl border-l-4 border-blue-600"><p class="text-lg italic font-medium">"A graça não é apenas o ponto de partida; é o combustível de toda a jornada cristã. Fomos salvos pela graça, somos mantidos pela graça e seremos glorificados pela graça." — Soli Deo Gloria.</p></div>',
            'full_content' => $fullContent,
            'sermon_structure_type' => 'expositivo',
            'status' => 'published',
            'visibility' => 'public',
            'is_featured' => true,
            'published_at' => now(),
            'sermon_date' => now()->subDays(2),
            'views' => 1250,
            'likes' => 450,
            'downloads' => 89
        ]);

        // 5. Vincular Tags
        $sermon->tags()->sync($tagIds);

        // 6. Referências Bíblicas com Notas de Exegese Pesadas
        SermonBibleReference::where('sermon_id', $sermon->id)->delete();

        SermonBibleReference::create([
            'sermon_id' => $sermon->id,
            'book' => 'Efésios',
            'chapter' => 2,
            'verses' => '1-3',
            'reference_text' => 'Ele vos vivificou, estando vós mortos nos vossos delitos e pecados...',
            'type' => 'support',
            'context' => 'Contexto de morte espiritual absoluta antes da regeneração.',
            'exegesis_notes' => 'O uso do dativo de causa para "delitos" e "pecados" indica que a morte não era apenas um estado, mas o resultado direto da transgressão ativa. O termo "delitos" (paraptoma) sugere um falso passo, enquanto "pecados" (hamartia) foca na incapacidade moral de atingir a glória.',
            'order' => 1
        ]);

        SermonBibleReference::create([
            'sermon_id' => $sermon->id,
            'book' => 'Efésios',
            'chapter' => 2,
            'verses' => '4-7',
            'reference_text' => 'Mas Deus, que é riquíssimo em misericórdia...',
            'type' => 'main',
            'context' => 'A intervenção divina como o ponto de virada da história.',
            'exegesis_notes' => 'O termo "Mas" (De) no v. 4 é possivelmente uma das palavras mais importantes da Bíblia. Ele introduz o agens (agente) da mudança. "Misericórdia" (eleos) foca no alívio da miséria do transgressor.',
            'order' => 2
        ]);

        SermonBibleReference::create([
            'sermon_id' => $sermon->id,
            'book' => 'Efésios',
            'chapter' => 2,
            'verses' => '8-10',
            'reference_text' => 'Porque pela graça sois salvos, por meio da fé...',
            'type' => 'main',
            'context' => 'A fórmula da salvação paulina.',
            'exegesis_notes' => 'Fé (pistis) aqui é o instrumento. A construção grega "te gar chariti este sesosmenoi" utiliza a ênfase no artigo para mostrar a singularidade da Graça cristã como única fonte de resgate.',
            'order' => 3
        ]);

        // 7. Adicionar uma nota exegética independente (SermonExegesis)
        SermonExegesis::withTrashed()->where('verse_start', 8)->where('book', 'Efésios')->where('chapter', 2)->forceDelete();

        SermonExegesis::create([
            'user_id' => $user->id,
            'verse_start' => 8,
            'book' => 'Efésios',
            'chapter' => 2,
            'title' => 'Análise de "Chariti" vs "Pistis"',
            'content' => 'A distinção entre a Graça (favor imerecido de Deus) e a Fé (confiança depositada na obra de Cristo). É vital notar que em Efésios 2:8, o salvo é passivo na regeneração (soa salvos - voz passiva), enquanto a fé é o ato de responder ao que o Espírito plantou.',
            'status' => 'published',
            'is_official' => true
        ]);

        $outlineSlug = 'esboco-rapido-efesios-2-1-10';
        SermonOutline::withTrashed()->where('slug', $outlineSlug)->forceDelete();

        // 8. Criar um Esboço Rápido (SermonOutline)
        SermonOutline::create([
            'slug' => $outlineSlug,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'sermon_series_id' => $series->id,
            'title' => 'Esboço Rápido: A Anatomia da Salvação',
            'description' => 'Um resumo em 3 pontos para pregações rápidas ou conferências.',
            'content' => '1. O Problema: Morte Espiritual. 2. A Solução: Graça Soberana. 3. O Propósito: Vida Transbordante.',
            'status' => 'published',
            'is_featured' => true
        ]);
    }
}
