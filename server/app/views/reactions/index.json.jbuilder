json.array!(@reactions) do |reaction|
  json.extract! reaction, :id, :description
  json.url reaction_url(reaction, format: :json)
end
