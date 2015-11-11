# -*- encoding: utf-8 -*-
# stub: celluloid-fsm 0.20.5 ruby lib

Gem::Specification.new do |s|
  s.name = "celluloid-fsm"
  s.version = "0.20.5"

  s.required_rubygems_version = Gem::Requirement.new(">= 0") if s.respond_to? :required_rubygems_version=
  s.require_paths = ["lib"]
  s.authors = ["Tony Arcieri", "Tim Carey-Smith", "Donovan Keme"]
  s.date = "2015-09-30"
  s.description = "Simple finite state machines with integrated Celluloid timeout support."
  s.email = ["tony.arcieri@gmail.com", "code@extremist.digital"]
  s.homepage = "https://github.com/celluloid/celluloid-fsm"
  s.licenses = ["MIT"]
  s.rubygems_version = "2.4.5"
  s.summary = "Celluloid Finite State Machines"

  s.installed_by_version = "2.4.5" if s.respond_to? :installed_by_version

  if s.respond_to? :specification_version then
    s.specification_version = 4

    if Gem::Version.new(Gem::VERSION) >= Gem::Version.new('1.2.0') then
      s.add_development_dependency(%q<bundler>, [">= 0"])
      s.add_development_dependency(%q<nenv>, [">= 0"])
      s.add_development_dependency(%q<dotenv>, [">= 0"])
      s.add_development_dependency(%q<benchmark_suite>, [">= 0"])
      s.add_development_dependency(%q<rubocop>, [">= 0"])
      s.add_development_dependency(%q<transpec>, [">= 0"])
      s.add_development_dependency(%q<pry>, [">= 0"])
      s.add_development_dependency(%q<rake>, [">= 0"])
      s.add_development_dependency(%q<rspec>, [">= 0"])
      s.add_development_dependency(%q<guard-rspec>, [">= 0"])
      s.add_development_dependency(%q<rspec-retry>, [">= 0"])
      s.add_development_dependency(%q<coveralls>, [">= 0"])
      s.add_development_dependency(%q<celluloid>, [">= 0.17.2"])
      s.add_development_dependency(%q<celluloid-essentials>, [">= 0"])
      s.add_development_dependency(%q<celluloid-supervision>, [">= 0"])
      s.add_development_dependency(%q<celluloid-pool>, [">= 0"])
      s.add_development_dependency(%q<celluloid-extras>, [">= 0"])
      s.add_runtime_dependency(%q<timers>, [">= 4.1.1"])
    else
      s.add_dependency(%q<bundler>, [">= 0"])
      s.add_dependency(%q<nenv>, [">= 0"])
      s.add_dependency(%q<dotenv>, [">= 0"])
      s.add_dependency(%q<benchmark_suite>, [">= 0"])
      s.add_dependency(%q<rubocop>, [">= 0"])
      s.add_dependency(%q<transpec>, [">= 0"])
      s.add_dependency(%q<pry>, [">= 0"])
      s.add_dependency(%q<rake>, [">= 0"])
      s.add_dependency(%q<rspec>, [">= 0"])
      s.add_dependency(%q<guard-rspec>, [">= 0"])
      s.add_dependency(%q<rspec-retry>, [">= 0"])
      s.add_dependency(%q<coveralls>, [">= 0"])
      s.add_dependency(%q<celluloid>, [">= 0.17.2"])
      s.add_dependency(%q<celluloid-essentials>, [">= 0"])
      s.add_dependency(%q<celluloid-supervision>, [">= 0"])
      s.add_dependency(%q<celluloid-pool>, [">= 0"])
      s.add_dependency(%q<celluloid-extras>, [">= 0"])
      s.add_dependency(%q<timers>, [">= 4.1.1"])
    end
  else
    s.add_dependency(%q<bundler>, [">= 0"])
    s.add_dependency(%q<nenv>, [">= 0"])
    s.add_dependency(%q<dotenv>, [">= 0"])
    s.add_dependency(%q<benchmark_suite>, [">= 0"])
    s.add_dependency(%q<rubocop>, [">= 0"])
    s.add_dependency(%q<transpec>, [">= 0"])
    s.add_dependency(%q<pry>, [">= 0"])
    s.add_dependency(%q<rake>, [">= 0"])
    s.add_dependency(%q<rspec>, [">= 0"])
    s.add_dependency(%q<guard-rspec>, [">= 0"])
    s.add_dependency(%q<rspec-retry>, [">= 0"])
    s.add_dependency(%q<coveralls>, [">= 0"])
    s.add_dependency(%q<celluloid>, [">= 0.17.2"])
    s.add_dependency(%q<celluloid-essentials>, [">= 0"])
    s.add_dependency(%q<celluloid-supervision>, [">= 0"])
    s.add_dependency(%q<celluloid-pool>, [">= 0"])
    s.add_dependency(%q<celluloid-extras>, [">= 0"])
    s.add_dependency(%q<timers>, [">= 4.1.1"])
  end
end
